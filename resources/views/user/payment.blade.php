<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #{{ $trxId }} - NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">

    @if (!empty($snapToken) && !empty($clientKey))
        <!-- Midtrans Snap JS -->
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    @endif

    <style>
        .payment-page-container {
            max-width: 900px;
            margin: 40px auto 80px;
            padding: 0 20px;
        }

        .payment-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #dde7f3;
            box-shadow: 0 6px 24px rgba(8, 124, 255, 0.07), 0 2px 8px rgba(16, 36, 92, 0.04);
            padding: 36px 32px;
        }

        .payment-header {
            text-align: center;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 24px;
            margin-bottom: 28px;
        }

        .payment-status-pill {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            background: #fef3c7;
            color: #b45309;
            margin-bottom: 12px;
        }

        .payment-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px;
        }

        .payment-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .payment-amount-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            margin-bottom: 28px;
        }

        .payment-amount-label {
            font-size: 13px;
            color: #1e40af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .payment-amount-value {
            font-size: 32px;
            font-weight: 800;
            color: #087cff;
        }

        .payment-items-section {
            margin-bottom: 30px;
        }

        .payment-items-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
        }

        .payment-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .payment-item-row:last-child {
            border-bottom: none;
        }

        .payment-item-details {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .payment-item-thumb {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .payment-item-name {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        .payment-item-qty {
            font-size: 13px;
            color: #64748b;
        }

        .payment-item-price {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }

        .payment-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-pay-midtrans {
            width: 100%;
            background: #087cff;
            color: #ffffff;
            border: none;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.3);
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .btn-pay-midtrans:hover {
            background: #0066d6;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(8, 124, 255, 0.4);
        }

        .btn-orders-back {
            width: 100%;
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 13px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .btn-orders-back:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .midtrans-channels-note {
            margin-top: 20px;
            padding: 16px;
            background: #f8fafc;
            border-radius: 12px;
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
            text-align: center;
        }

        .midtrans-channels-note strong {
            color: #1e293b;
        }

        .alert-unconfigured {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            line-height: 1.6;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    @include('partials.navbar')

    <main class="payment-page-container">
        <div class="payment-card">

            <div class="payment-header">
                <span class="payment-status-pill">Menunggu Pembayaran</span>
                <h1 class="payment-title">Selesaikan Pembayaran Anda</h1>
                <p class="payment-subtitle">Nomor Transaksi: <strong>#{{ $trxId }}</strong> &bull; {{ date('d M Y, H:i', strtotime($firstOrder->created_at ?? now())) }}</p>
            </div>

            @if(!$isConfigured)
                <div class="alert-unconfigured">
                    <strong>Perhatian:</strong> Kredensial Midtrans Sandbox belum diisi pada file <code>.env</code> project.
                    Silakan masukkan <code>MIDTRANS_SERVER_KEY</code> dan <code>MIDTRANS_CLIENT_KEY</code> untuk mengaktifkan pembayaran Snap.
                </div>
            @endif

            <!-- NOMINAL TOTAL -->
            <div class="payment-amount-box">
                <div class="payment-amount-label">Total yang Harus Dibayar</div>
                <div class="payment-amount-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>

            <!-- DETAIL ITEM BELANJA -->
            <div class="payment-items-section">
                <h2 class="payment-items-title">Rincian Produk</h2>
                @foreach ($orders as $item)
                    <div class="payment-item-row">
                        <div class="payment-item-details">
                            @if (!empty($item->product_image))
                                <img src="{{ asset('assets/images/' . $item->product_image) }}" alt="{{ $item->product_title }}" class="payment-item-thumb">
                            @else
                                <div class="payment-item-thumb" style="display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:10px;">No Img</div>
                            @endif
                            <div>
                                <div class="payment-item-name">{{ $item->product_title }}</div>
                                <div class="payment-item-qty">{{ $item->qty }} unit &times; Rp {{ number_format($item->product_price, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="payment-item-price">
                            Rp {{ number_format($item->qty * $item->product_price, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- TOMBOL AKSI -->
            <div class="payment-actions">
                @if (!empty($snapToken))
                    <button type="button" class="btn-pay-midtrans" id="payButton">
                        Bayar Sekarang dengan Midtrans
                    </button>
                @else
                    <button type="button" class="btn-pay-midtrans" style="opacity: 0.6; cursor: not-allowed;" disabled>
                        Sesi Pembayaran Belum Tersedia
                    </button>
                @endif

                <a href="{{ route('user.orders', ['tab' => 'orders']) }}" class="btn-orders-back">
                    Bayar Nanti / Lihat Pesanan Saya
                </a>
            </div>

            <div class="midtrans-channels-note">
                Metode yang didukung via Midtrans: <strong>QRIS (GoPay, OVO, Dana)</strong>, <strong>BCA / Mandiri / BRI / BNI Virtual Account</strong>, <strong>ShopeePay</strong>, dan <strong>Kartu Kredit / Debit</strong>.
                Transaksi diproses dengan enkripsi keamanan standar internasional.
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

    @if (!empty($snapToken) && !empty($clientKey))
        <script>
            const payButton = document.getElementById('payButton');

            function triggerSnapPayment() {
                if (typeof window.snap === 'undefined') {
                    alert('Script pembayaran Midtrans sedang dimuat. Silakan tunggu beberapa detik dan coba lagi.');
                    return;
                }

                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        window.location.href = "{{ route('user.payment.finish') }}?order_id={{ $trxId }}&status_code=200&transaction_status=settlement";
                    },
                    onPending: function(result) {
                        window.location.href = "{{ route('user.payment.finish') }}?order_id={{ $trxId }}&status_code=201&transaction_status=pending";
                    },
                    onError: function(result) {
                        window.location.href = "{{ route('user.payment.finish') }}?order_id={{ $trxId }}&status_code=202&transaction_status=deny";
                    },
                    onClose: function() {
                        console.log('Jendela pembayaran ditutup oleh pengguna.');
                    }
                });
            }

            if (payButton) {
                payButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    triggerSnapPayment();
                });
            }

            // Otomatis buka Snap modal saat halaman pertama kali dimuat jika ada token
            window.addEventListener('load', function() {
                // Tunggu 500ms agar halaman siap
                setTimeout(function() {
                    if (typeof window.snap !== 'undefined') {
                        triggerSnapPayment();
                    }
                }, 500);
            });
        </script>
    @endif

</body>
</html>
