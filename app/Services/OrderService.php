<?php

namespace App\Services;

use App\Models\OrderStatusHistory;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Catat riwayat status pesanan
     */
    public function recordHistory(
        string $trxId,
        string $status,
        string $title,
        ?string $description = null,
        string $actorType = 'system',
        ?int $actorId = null,
        ?string $actorName = null,
        ?int $orderId = null
    ): OrderStatusHistory {
        return OrderStatusHistory::record(
            trxId: $trxId,
            status: $status,
            title: $title,
            description: $description,
            actorType: $actorType,
            actorId: $actorId,
            actorName: $actorName,
            orderId: $orderId
        );
    }

    /**
     * Tandai pembayaran berhasil via Midtrans (idempotent)
     */
    public function markPaymentSuccess(
        string $trxId,
        ?string $paymentType = null,
        ?string $transactionId = null,
        ?string $midtransStatus = 'settlement'
    ): bool {
        return DB::transaction(function () use ($trxId, $paymentType, $transactionId, $midtransStatus) {
            $orders = DB::table('orders')->where('trx_id', $trxId)->lockForUpdate()->get();

            if ($orders->isEmpty()) {
                return false;
            }

            $first = $orders->first();
            $currentOrderStatus = $first->order_status ?? 'menunggu_pembayaran';
            $currentPaymentStatus = strtolower((string) ($first->p_status ?? ''));

            // Data pembaruan
            $updateData = [
                'p_status' => 'Completed',
                'midtrans_status' => $midtransStatus ?? 'settlement',
                'paid_at' => $first->paid_at ?? now(),
                'updated_at' => now(),
            ];

            if ($paymentType) {
                $updateData['midtrans_payment_type'] = $paymentType;
            }
            if ($transactionId) {
                $updateData['midtrans_transaction_id'] = $transactionId;
            }

            // Hanya perbarui order_status jika masih menunggu pembayaran
            $shouldUpdateOrderStatus = in_array($currentOrderStatus, ['menunggu_pembayaran', 'pending', '']);
            if ($shouldUpdateOrderStatus) {
                $updateData['order_status'] = 'pembayaran_berhasil';
            }

            DB::table('orders')->where('trx_id', $trxId)->update($updateData);

            // Catat history jika sebelumnya belum lunas
            if ($currentPaymentStatus !== 'completed' && $currentPaymentStatus !== 'selesai') {
                $typeStr = $paymentType ? " via " . ucfirst(str_replace('_', ' ', $paymentType)) : '';
                $this->recordHistory(
                    trxId: $trxId,
                    status: 'pembayaran_berhasil',
                    title: 'Pembayaran Berhasil',
                    description: "Pembayaran{$typeStr} telah diverifikasi otomatis oleh Midtrans. Pesanan siap diproses oleh Admin.",
                    actorType: 'system',
                    actorId: null,
                    actorName: 'Midtrans Webhook',
                    orderId: $first->order_id ?? null
                );
            }

            return true;
        });
    }

    /**
     * Admin memproses pesanan (menjadi 'sedang_diproses')
     */
    public function processOrder(string $trxId, int $adminId, string $adminName): array
    {
        return DB::transaction(function () use ($trxId, $adminId, $adminName) {
            $orders = DB::table('orders')->where('trx_id', $trxId)->lockForUpdate()->get();

            if ($orders->isEmpty()) {
                return ['success' => false, 'message' => 'Pesanan tidak ditemukan.'];
            }

            $first = $orders->first();
            $orderStatus = $first->order_status ?? 'menunggu_pembayaran';

            // Validasi: Hanya pesanan dengan status 'pembayaran_berhasil' (atau COD) yang dapat diproses
            if ($orderStatus === 'menunggu_pembayaran' && strtolower($first->payment_method ?? '') === 'midtrans') {
                return [
                    'success' => false,
                    'message' => 'Pesanan belum dapat diproses karena masih menunggu konfirmasi pembayaran Midtrans.',
                ];
            }

            if ($orderStatus === 'sedang_diproses') {
                return ['success' => false, 'message' => 'Pesanan sudah dalam status sedang diproses.'];
            }

            if (in_array($orderStatus, ['dikirim', 'selesai'])) {
                return ['success' => false, 'message' => 'Pesanan sudah dikirim atau selesai.'];
            }

            if ($orderStatus === 'dibatalkan' || strtolower($first->p_status ?? '') === 'cancelled') {
                return ['success' => false, 'message' => 'Pesanan telah dibatalkan dan tidak dapat diproses.'];
            }

            DB::table('orders')->where('trx_id', $trxId)->update([
                'order_status' => 'sedang_diproses',
                'processed_at' => now(),
                'updated_at' => now(),
            ]);

            $this->recordHistory(
                trxId: $trxId,
                status: 'sedang_diproses',
                title: 'Pesanan Sedang Diproses',
                description: "Pesanan telah dikonfirmasi dan sedang disiapkan oleh Admin ({$adminName}).",
                actorType: 'admin',
                actorId: $adminId,
                actorName: $adminName,
                orderId: $first->order_id ?? null
            );

            return ['success' => true, 'message' => "Pesanan #{$trxId} berhasil diproses."];
        });
    }

    /**
     * Admin mengirim pesanan (menjadi 'dikirim')
     */
    public function shipOrder(
        string $trxId,
        string $courierName,
        string $trackingNumber,
        ?string $shippingNotes,
        int $adminId,
        string $adminName
    ): array {
        return DB::transaction(function () use ($trxId, $courierName, $trackingNumber, $shippingNotes, $adminId, $adminName) {
            $orders = DB::table('orders')->where('trx_id', $trxId)->lockForUpdate()->get();

            if ($orders->isEmpty()) {
                return ['success' => false, 'message' => 'Pesanan tidak ditemukan.'];
            }

            $first = $orders->first();
            $orderStatus = $first->order_status ?? 'menunggu_pembayaran';

            if ($orderStatus !== 'sedang_diproses') {
                return [
                    'success' => false,
                    'message' => 'Pesanan harus dalam status Sedang Diproses terlebih dahulu sebelum dikirim.',
                ];
            }

            $courierClean = trim($courierName);
            $resiClean = trim($trackingNumber);

            if (empty($courierClean) || empty($resiClean)) {
                return [
                    'success' => false,
                    'message' => 'Nama kurir dan nomor resi wajib diisi.',
                ];
            }

            DB::table('orders')->where('trx_id', $trxId)->update([
                'order_status' => 'dikirim',
                'courier_name' => $courierClean,
                'tracking_number' => $resiClean,
                'shipping_notes' => $shippingNotes ? trim($shippingNotes) : null,
                'shipped_at' => now(),
                'updated_at' => now(),
            ]);

            $desc = "Pesanan telah diserahkan kepada kurir {$courierClean} dengan No. Resi: {$resiClean}.";
            if (!empty($shippingNotes)) {
                $desc .= " Catatan: " . trim($shippingNotes);
            }

            $this->recordHistory(
                trxId: $trxId,
                status: 'dikirim',
                title: 'Pesanan Dikirim',
                description: $desc,
                actorType: 'admin',
                actorId: $adminId,
                actorName: $adminName,
                orderId: $first->order_id ?? null
            );

            return ['success' => true, 'message' => "Pesanan #{$trxId} berhasil dikirim dengan kurir {$courierClean} (Resi: {$resiClean})."];
        });
    }

    /**
     * User mengonfirmasi penerimaan barang ('Pesanan Diterima' -> 'selesai')
     */
    public function confirmReceived(string $trxId, int $userId, string $userName): array
    {
        return DB::transaction(function () use ($trxId, $userId, $userName) {
            $orders = DB::table('orders')
                ->where('trx_id', $trxId)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            if ($orders->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.',
                ];
            }

            $first = $orders->first();
            $orderStatus = $first->order_status ?? 'menunggu_pembayaran';

            if ($orderStatus === 'selesai') {
                return [
                    'success' => true,
                    'message' => 'Pesanan ini sudah berstatus selesai.',
                ];
            }

            if ($orderStatus !== 'dikirim') {
                return [
                    'success' => false,
                    'message' => 'Konfirmasi penerimaan hanya dapat dilakukan jika pesanan sudah dalam status Dikirim.',
                ];
            }

            DB::table('orders')
                ->where('trx_id', $trxId)
                ->where('user_id', $userId)
                ->update([
                    'order_status' => 'selesai',
                    'completed_at' => now(),
                    'updated_at' => now(),
                ]);

            $this->recordHistory(
                trxId: $trxId,
                status: 'selesai',
                title: 'Pesanan Selesai',
                description: "Pesanan telah diterima dengan baik oleh pembeli ({$userName}). Transaksi selesai.",
                actorType: 'user',
                actorId: $userId,
                actorName: $userName,
                orderId: $first->order_id ?? null
            );

            return [
                'success' => true,
                'message' => "Terima kasih! Pesanan #{$trxId} telah berhasil dikonfirmasi selesai.",
            ];
        });
    }

    /**
     * Batalkan pesanan dan pulihkan stok produk
     */
    public function cancelOrder(
        string $trxId,
        string $reason,
        string $actorType = 'system',
        ?int $actorId = null,
        ?string $actorName = null,
        ?string $midtransStatus = null
    ): array {
        return DB::transaction(function () use ($trxId, $reason, $actorType, $actorId, $actorName, $midtransStatus) {
            $orders = DB::table('orders')->where('trx_id', $trxId)->lockForUpdate()->get();

            if ($orders->isEmpty()) {
                return ['success' => false, 'message' => 'Pesanan tidak ditemukan.'];
            }

            $first = $orders->first();
            $currentStatus = strtolower((string) ($first->p_status ?? ''));
            $orderStatus = $first->order_status ?? 'menunggu_pembayaran';

            if (in_array($orderStatus, ['dikirim', 'selesai'])) {
                return ['success' => false, 'message' => 'Pesanan yang sudah dikirim atau selesai tidak dapat dibatalkan.'];
            }

            if ($currentStatus === 'cancelled' || $orderStatus === 'dibatalkan') {
                return ['success' => true, 'message' => 'Pesanan sudah berstatus dibatalkan.'];
            }

            // Kembalikan stok produk
            foreach ($orders as $item) {
                DB::table('products')
                    ->where('product_id', $item->product_id)
                    ->increment('stock', (int) $item->qty);
            }

            DB::table('orders')->where('trx_id', $trxId)->update([
                'p_status' => 'Cancelled',
                'order_status' => 'dibatalkan',
                'midtrans_status' => $midtransStatus ?? 'cancelled',
                'updated_at' => now(),
            ]);

            $this->recordHistory(
                trxId: $trxId,
                status: 'dibatalkan',
                title: 'Pesanan Dibatalkan',
                description: "Pesanan dibatalkan. Alasan: {$reason}. Stok produk telah dikembalikan.",
                actorType: $actorType,
                actorId: $actorId,
                actorName: $actorName,
                orderId: $first->order_id ?? null
            );

            return ['success' => true, 'message' => "Pesanan #{$trxId} berhasil dibatalkan dan stok produk telah dipulihkan."];
        });
    }
}
