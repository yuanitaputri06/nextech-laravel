<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">

    <style>
        .finish-page-container {
            max-width: 700px;
            margin: 50px auto 90px;
            padding: 0 20px;
        }

        .finish-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #dde7f3;
            box-shadow: 0 6px 28px rgba(8, 124, 255, 0.08), 0 2px 8px rgba(16, 36, 92, 0.04);
            padding: 40px 32px;
            text-align: center;
        }

        .finish-icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .finish-icon-success {
            background: #dcfce7;
            color: #15803d;
        }

        .finish-icon-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .finish-icon-failed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .finish-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 10px;
        }

        .finish-desc {
            font-size: 15px;
            color: #64748b;
            line-height: 1.6;
            max-width: 500px;
            margin: 0 auto 28px;
        }

        .finish-details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
        }

        .finish-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .finish-row:last-child {
            border-bottom: none;
            padding-top: 12px;
            font-weight: 700;
            font-size: 16px;
        }

        .finish-row-label {
            color: #64748b;
        }

        .finish-row-value {
            color: #0f172a;
            font-weight: 600;
        }

        .finish-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-finish-primary {
            background: #087cff;
            color: #ffffff;
            padding: 13px 26px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-finish-primary:hover {
            background: #0066d6;
        }

        .btn-finish-secondary {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
            padding: 13px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .btn-finish-secondary:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    @include('partials.navbar')

    @php
        $statusLower = strtolower((string) $transactionStatus);
        $isSuccess = in_array($statusLower, ['settlement', 'capture', 'completed', 'success']);
        $isPending = in_array($statusLower, ['pending']);
        $isFailed = in_array($statusLower, ['deny', 'cancel', 'expire', 'cancelled', 'failure']);
    @endphp

    <main class="finish-page-container">
        <div class="finish-card">

            @if ($isSuccess)
                <div class="finish-icon-circle finish-icon-success">
                    <i data-lucide="circle-check" style="width: 38px; height: 38px;"></i>
                </div>
                <h1 class="finish-title">Pembayaran Berhasil</h1>
                <p class="finish-desc">
                    Terima kasih! Pembayaran untuk pesanan Anda telah berhasil diverifikasi oleh sistem Midtrans. Pesanan Anda segera kami siapkan dan kirimkan.
                </p>
            @elseif ($isPending)
                <div class="finish-icon-circle finish-icon-pending">
                    <i data-lucide="clock" style="width: 38px; height: 38px;"></i>
                </div>
                <h1 class="finish-title">Menunggu Pembayaran</h1>
                <p class="finish-desc">
                    Pesanan Anda telah dicatat. Silakan selesaikan pembayaran sesuai petunjuk metode yang Anda pilih sebelum batas waktu berakhir.
                </p>
            @else
                <div class="finish-icon-circle finish-icon-failed">
                    <i data-lucide="circle-x" style="width: 38px; height: 38px;"></i>
                </div>
                <h1 class="finish-title">Pembayaran Belum Berhasil</h1>
                <p class="finish-desc">
                    Pembayaran Anda telah dibatalkan, ditolak, atau melewati batas waktu transaksi. Anda dapat melakukan pemesanan ulang kapan saja.
                </p>
            @endif

            <!-- DETAIL TRANSAKSI -->
            <div class="finish-details-box">
                <div class="finish-row">
                    <span class="finish-row-label">Nomor Transaksi</span>
                    <span class="finish-row-value">#{{ $trxId ?: 'N/A' }}</span>
                </div>
                <div class="finish-row">
                    <span class="finish-row-label">Status Transaksi</span>
                    <span class="finish-row-value">
                        @if ($isSuccess)
                            <span style="color: #15803d; font-weight: 700;">Selesai / Lunas</span>
                        @elseif ($isPending)
                            <span style="color: #b45309; font-weight: 700;">Menunggu Pembayaran</span>
                        @else
                            <span style="color: #b91c1c; font-weight: 700;">Dibatalkan / Gagal</span>
                        @endif
                    </span>
                </div>
                <div class="finish-row">
                    <span class="finish-row-label">Metode Pembayaran</span>
                    <span class="finish-row-value">Midtrans Payment Gateway</span>
                </div>
                @if ($totalAmount > 0)
                    <div class="finish-row">
                        <span class="finish-row-label">Total Pembayaran</span>
                        <span class="finish-row-value" style="color: #087cff;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <!-- TOMBOL AKSI -->
            <div class="finish-actions">
                <a href="{{ route('user.orders', ['tab' => 'orders']) }}" class="btn-finish-primary">
                    Lihat Pesanan Saya
                </a>
                <a href="{{ route('products.index') }}" class="btn-finish-secondary">
                    Lanjut Belanja
                </a>
            </div>

        </div>
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    <!-- CHATBOT -->
    @include('partials.chatbot')

</body>
</html>
