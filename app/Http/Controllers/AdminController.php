<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Menampilkan Dashboard Admin dengan integrasi dua database:
     * 1. Database Utama (ecommerce / connection 'mysql'): Produk, User, Kategori, Pesanan, Analisis & Grafik.
     * 2. Database Log (ecommerce_logs / connection 'mysql_logs'): Riwayat Aktivitas Admin.
     */
    public function dashboard(Request $request)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        // 1. Data Statistik Operasional Utama (Database Utama)
        $totalProducts = DB::connection('mysql')->table('products')->count();
        $totalUsers = DB::connection('mysql')->table('user_info')->count();
        $totalCategories = DB::connection('mysql')->table('categories')->count();
        $totalOrders = DB::connection('mysql')->table('orders')->count();

        // Filter Periode Analisis: 'all', 'year', 'month', '30days', '7days'
        $period = $request->query('period', 'all');
        $now = now();
        $dateFilter = null;
        if ($period === '7days') {
            $dateFilter = $now->copy()->subDays(7);
        } elseif ($period === '30days') {
            $dateFilter = $now->copy()->subDays(30);
        } elseif ($period === 'month') {
            $dateFilter = $now->copy()->startOfMonth();
        } elseif ($period === 'year') {
            $dateFilter = $now->copy()->startOfYear();
        }

        // Query pesanan berbasis periode
        $ordersQuery = DB::connection('mysql')->table('orders');
        if ($dateFilter) {
            $ordersQuery->where('orders.created_at', '>=', $dateFilter);
        }

        $analyticsTotalOrders = (clone $ordersQuery)->count();

        // Kriteria transaksi berhasil dibayar (LUNAS)
        $isPaidCondition = function ($q) {
            $q->where('orders.p_status', 'Completed')
              ->orWhereIn('orders.order_status', ['pembayaran_berhasil', 'sedang_diproses', 'dikirim', 'selesai']);
        };

        // Pesanan yang sudah dibayar
        $paidOrdersCount = (clone $ordersQuery)->where($isPaidCondition)->count();

        // Total pendapatan dari transaksi yang berhasil dibayar
        $revenueQuery = DB::connection('mysql')->table('orders')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->where($isPaidCondition);
        if ($dateFilter) {
            $revenueQuery->where('orders.created_at', '>=', $dateFilter);
        }
        $totalRevenue = (int) ((clone $revenueQuery)->selectRaw('SUM(COALESCE(orders.total_amt, orders.qty * products.product_price)) as rev')->value('rev') ?? 0);

        // A. GRAFIK PENDAPATAN BULANAN (Hanya transaksi berhasil)
        $monthlyQuery = DB::connection('mysql')->table('orders')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->where($isPaidCondition);
        if ($dateFilter) {
            $monthlyQuery->where('orders.created_at', '>=', $dateFilter);
        }
        $monthlyRaw = $monthlyQuery
            ->selectRaw("DATE_FORMAT(COALESCE(orders.paid_at, orders.created_at), '%Y-%m') as ym, DATE_FORMAT(COALESCE(orders.paid_at, orders.created_at), '%b %Y') as month_label, SUM(COALESCE(orders.total_amt, orders.qty * products.product_price)) as revenue, COUNT(orders.order_id) as count")
            ->groupBy('ym', 'month_label')
            ->orderBy('ym', 'asc')
            ->get();

        $chartRevenueLabels = $monthlyRaw->pluck('month_label')->toArray();
        $chartRevenueData = $monthlyRaw->map(fn($item) => (int) $item->revenue)->toArray();

        // B. DISTRIBUSI STATUS PEMBAYARAN (Doughnut Chart)
        // Sudah Dibayar
        $paymentPaidCount = $paidOrdersCount;

        // Menunggu Pembayaran
        $paymentWaitingCount = (clone $ordersQuery)
            ->where(function ($q) {
                $q->where('order_status', 'menunggu_pembayaran')
                  ->orWhere('p_status', 'Pending');
            })
            ->where('p_status', '!=', 'Completed')
            ->whereNotIn('order_status', ['pembayaran_berhasil', 'sedang_diproses', 'dikirim', 'selesai', 'dibatalkan', 'gagal'])
            ->count();

        // Gagal atau Ditolak
        $paymentFailedCount = (clone $ordersQuery)
            ->where(function ($q) {
                $q->whereIn('midtrans_status', ['deny', 'failure'])
                  ->orWhere('order_status', 'gagal');
            })->count();

        // Kedaluwarsa atau Dibatalkan
        $paymentCancelledCount = (clone $ordersQuery)
            ->where(function ($q) {
                $q->where('order_status', 'dibatalkan')
                  ->orWhereIn('midtrans_status', ['expire', 'cancel']);
            })->count();

        $chartPaymentLabels = ['Sudah Dibayar', 'Menunggu Pembayaran', 'Gagal / Ditolak', 'Dibatalkan / Kedaluwarsa'];
        $chartPaymentData = [$paymentPaidCount, $paymentWaitingCount, $paymentFailedCount, $paymentCancelledCount];

        // Status Pengiriman / Operasional (tanpa perhitungan ganda)
        $processingOrders = (clone $ordersQuery)->where('order_status', 'sedang_diproses')->count();
        $shippedOrders = (clone $ordersQuery)->where('order_status', 'dikirim')->count();
        $completedOrders = (clone $ordersQuery)->where('order_status', 'selesai')->count();
        $pendingOrders = (clone $ordersQuery)->where('p_status', 'Pending')->count();

        // C. GRAFIK PRODUK PER KATEGORI (Bar Chart dinamis dari database)
        $categoriesData = DB::connection('mysql')->table('categories')
            ->leftJoin('products', 'categories.cat_id', '=', 'products.product_cat')
            ->select('categories.cat_id', 'categories.cat_title', DB::raw('COUNT(products.product_id) as total_products'))
            ->groupBy('categories.cat_id', 'categories.cat_title')
            ->orderBy('total_products', 'desc')
            ->get();

        $chartCategoryLabels = $categoriesData->pluck('cat_title')->toArray();
        $chartCategoryData = $categoriesData->pluck('total_products')->map(fn($v) => (int)$v)->toArray();

        // D. RINGKASAN PESANAN DAN PENDAPATAN
        $summary = [
            'total_orders' => $analyticsTotalOrders,
            'paid_orders' => $paidOrdersCount,
            'waiting_payment' => $paymentWaitingCount,
            'processing' => $processingOrders,
            'shipped' => $shippedOrders,
            'completed' => $completedOrders,
            'failed' => $paymentFailedCount,
            'cancelled' => $paymentCancelledCount,
            'total_revenue' => $totalRevenue,
        ];

        // 5 Produk terbaru untuk tabel di bawah grafik
        $products = DB::connection('mysql')->table('products')
            ->orderBy('product_id', 'desc')
            ->limit(5)
            ->get();

        // 2. Riwayat Aktivitas Admin (Database Kedua)
        $logsError = null;
        try {
            $activityLogs = ActivityLog::orderBy('created_at', 'desc')->paginate(10);
        } catch (\Throwable $e) {
            Log::error('Dashboard Admin: Gagal mengambil activity_logs: ' . $e->getMessage());
            $activityLogs = new LengthAwarePaginator([], 0, 10, 1, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
            $logsError = 'Koneksi ke database log aktivitas sedang mengalami gangguan.';
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'totalCategories',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'period',
            'summary',
            'chartRevenueLabels',
            'chartRevenueData',
            'chartPaymentLabels',
            'chartPaymentData',
            'chartCategoryLabels',
            'chartCategoryData',
            'categoriesData',
            'products',
            'activityLogs',
            'logsError'
        ));
    }

    /**
     * Manajemen Pesanan Sisi Admin
     * Mendukung pencarian, filter status pembayaran & status pesanan
     */
    public function orders(Request $request)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $search = trim((string) $request->query('q', ''));
        $paymentStatus = trim((string) $request->query('payment_status', 'all'));
        $orderStatus = trim((string) $request->query('order_status', 'all'));

        // Query data pesanan
        $query = DB::table('orders')
            ->leftJoin('user_info', 'orders.user_id', '=', 'user_info.user_id')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->select(
                'orders.*',
                'user_info.first_name',
                'user_info.last_name',
                'user_info.email as customer_email',
                'user_info.mobile as customer_mobile',
                'user_info.address1 as customer_address',
                'products.product_title',
                'products.product_price',
                'products.product_image'
            );

        // Filter Pencarian (Nomor Pesanan / Trx ID / Nama Pembeli / Email Produk)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.trx_id', 'like', "%{$search}%")
                    ->orWhere('orders.order_id', 'like', "%{$search}%")
                    ->orWhere('orders.tracking_number', 'like', "%{$search}%")
                    ->orWhere('user_info.first_name', 'like', "%{$search}%")
                    ->orWhere('user_info.last_name', 'like', "%{$search}%")
                    ->orWhere('user_info.email', 'like', "%{$search}%")
                    ->orWhere('products.product_title', 'like', "%{$search}%");
            });
        }

        // Filter Status Pembayaran
        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            $query->where('orders.p_status', $paymentStatus);
        }

        // Filter Status Pesanan
        if (!empty($orderStatus) && $orderStatus !== 'all') {
            $query->where('orders.order_status', $orderStatus);
        }

        $orders = $query->orderBy('orders.order_id', 'desc')->get();

        // Hitung statistik pesanan
        $stats = [
            'total' => DB::table('orders')->count(),
            'menunggu_pembayaran' => DB::table('orders')->where('order_status', 'menunggu_pembayaran')->count(),
            'pembayaran_berhasil' => DB::table('orders')->where('order_status', 'pembayaran_berhasil')->count(),
            'sedang_diproses' => DB::table('orders')->where('order_status', 'sedang_diproses')->count(),
            'dikirim' => DB::table('orders')->where('order_status', 'dikirim')->count(),
            'selesai' => DB::table('orders')->where('order_status', 'selesai')->count(),
            'dibatalkan' => DB::table('orders')->where('order_status', 'dibatalkan')->count(),
        ];

        return view('admin.orders', compact('orders', 'stats', 'search', 'paymentStatus', 'orderStatus'));
    }

    /**
     * Detail Pesanan Admin & Riwayat Status
     */
    public function orderDetail(Request $request, $trxId)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $orders = DB::table('orders')
            ->leftJoin('user_info', 'orders.user_id', '=', 'user_info.user_id')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->select(
                'orders.*',
                'user_info.first_name',
                'user_info.last_name',
                'user_info.email as customer_email',
                'user_info.mobile as customer_mobile',
                'user_info.address1 as customer_address',
                'products.product_title',
                'products.product_price',
                'products.product_image'
            )
            ->where(function ($q) use ($trxId) {
                $q->where('orders.trx_id', $trxId)
                    ->orWhere('orders.order_id', $trxId);
            })
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('admin.orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $firstOrder = $orders->first();
        $actualTrxId = $firstOrder->trx_id;

        // Ambil riwayat perubahan status
        $histories = DB::table('order_status_histories')
            ->where('trx_id', $actualTrxId)
            ->orderBy('id', 'asc')
            ->get();

        $totalAmount = $orders->sum(function ($item) {
            return (int) ($item->total_amt ?: ($item->qty * $item->product_price));
        });

        return view('admin.order-detail', compact('orders', 'firstOrder', 'histories', 'totalAmount', 'actualTrxId'));
    }

    /**
     * Admin Memproses Pesanan ('pembayaran_berhasil' -> 'sedang_diproses')
     */
    public function processOrder(Request $request, $trxId)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $adminId = (int) session('admin_id');
        $adminName = (string) (session('admin_name') ?? 'Admin');

        $result = $this->orderService->processOrder($trxId, $adminId, $adminName);

        if ($result['success']) {
            // Catat ke ActivityLog
            try {
                ActivityLog::record(
                    adminId: $adminId,
                    adminName: $adminName,
                    activityType: 'Memproses Pesanan',
                    description: "Memproses pesanan #{$trxId} menjadi Sedang Diproses",
                    subjectType: 'Order',
                    subjectId: null,
                    oldValues: ['order_status' => 'pembayaran_berhasil'],
                    newValues: ['order_status' => 'sedang_diproses'],
                    objectInfo: "Pesanan #{$trxId}"
                );
            } catch (\Throwable $e) {
                // Abaikan jika log error
            }

            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Admin Mengirim Pesanan ('sedang_diproses' -> 'dikirim')
     * Mengisi kurir, nomor resi, dan catatan pengiriman
     */
    public function shipOrder(Request $request, $trxId)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $request->validate([
            'courier_name' => 'required|string|max:100',
            'tracking_number' => 'required|string|max:100',
            'shipping_notes' => 'nullable|string|max:500',
        ], [
            'courier_name.required' => 'Nama jasa kurir wajib diisi.',
            'tracking_number.required' => 'Nomor resi pengiriman wajib diisi.',
        ]);

        $adminId = (int) session('admin_id');
        $adminName = (string) (session('admin_name') ?? 'Admin');

        $result = $this->orderService->shipOrder(
            $trxId,
            $request->courier_name,
            $request->tracking_number,
            $request->shipping_notes,
            $adminId,
            $adminName
        );

        if ($result['success']) {
            try {
                ActivityLog::record(
                    adminId: $adminId,
                    adminName: $adminName,
                    activityType: 'Mengirim Pesanan',
                    description: "Mengirim pesanan #{$trxId} dengan {$request->courier_name} (Resi: {$request->tracking_number})",
                    subjectType: 'Order',
                    subjectId: null,
                    oldValues: ['order_status' => 'sedang_diproses'],
                    newValues: [
                        'order_status' => 'dikirim',
                        'courier_name' => $request->courier_name,
                        'tracking_number' => $request->tracking_number,
                    ],
                    objectInfo: "Pesanan #{$trxId}"
                );
            } catch (\Throwable $e) {
                // Abaikan jika log error
            }

            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Menghapus pesanan
     */
    public function destroyOrder(Request $request, $id)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $order = DB::table('orders')->where('order_id', $id)->first();
        if (!$order) {
            return redirect()->route('admin.orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        DB::table('orders')->where('order_id', $id)->delete();

        try {
            ActivityLog::record(
                adminId: (int) session('admin_id'),
                adminName: (string) (session('admin_name') ?? 'Admin'),
                activityType: 'Menghapus Pesanan',
                description: "Menghapus pesanan #{$id} ({$order->trx_id})",
                subjectType: 'Order',
                subjectId: (int) $id,
                oldValues: (array) $order,
                newValues: [],
                objectInfo: "Pesanan #{$id}"
            );
        } catch (\Throwable $e) {
            // ignore
        }

        return redirect()->route('admin.orders')->with('success', "Pesanan #{$id} berhasil dihapus.");
    }

    /**
     * Menampilkan daftar pesanan yang sudah selesai (Completed)
     */
    public function completedOrders()
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $orders = DB::table('orders')
            ->leftJoin('user_info', 'orders.user_id', '=', 'user_info.user_id')
            ->leftJoin('products', 'orders.product_id', '=', 'products.product_id')
            ->select(
                'orders.*',
                'user_info.first_name',
                'user_info.last_name',
                'products.product_title',
                'products.product_price'
            )
            ->where(function ($q) {
                $q->where('orders.p_status', 'Completed')
                    ->orWhere('orders.order_status', 'selesai');
            })
            ->orderBy('orders.order_id', 'desc')
            ->get();

        return view('admin.orders-completed', compact('orders'));
    }

    /**
     * Memperbarui status pesanan (kompatibilitas backward dan activity log)
     */
    public function updateOrderStatus(Request $request, $id)
    {
        if (!session('is_admin')) {
            return redirect()->route('login');
        }

        $request->validate([
            'p_status' => 'required|string|max:50',
        ]);

        $order = DB::table('orders')->where('order_id', $id)->first();
        if (!$order) {
            return redirect()->route('admin.orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $oldStatus = $order->p_status;
        $newStatus = $request->p_status;

        DB::table('orders')->where('order_id', $id)->update([
            'p_status' => $newStatus,
            'updated_at' => now(),
        ]);

        try {
            ActivityLog::record(
                adminId: session('admin_id'),
                adminName: session('admin_name'),
                activityType: 'Mengubah Status Pesanan',
                description: "Mengubah status pesanan #{$id} dari '{$oldStatus}' menjadi '{$newStatus}'",
                subjectType: 'Order',
                subjectId: (int) $id,
                oldValues: [
                    'order_id' => $order->order_id,
                    'p_status' => $oldStatus,
                ],
                newValues: [
                    'order_id' => $order->order_id,
                    'p_status' => $newStatus,
                ],
                objectInfo: "Pesanan #{$id} ({$oldStatus} -> {$newStatus})"
            );
        } catch (\Throwable $e) {
            // Ignore jika database log bermasalah
        }

        return redirect()->route('admin.orders')->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
