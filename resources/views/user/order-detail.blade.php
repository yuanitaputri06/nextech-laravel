<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTech - Detail & Pelacakan Pesanan #{{ $trxId }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .page-container {
            width: 90%;
            max-width: 1100px;
            margin: 36px auto 60px;
            min-height: 65vh;
        }

        .back-nav {
            margin-bottom: 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #087cff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .order-header-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.03);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .order-header-info h1 {
            font-size: 22px;
            font-weight: 800;
            color: #10245c;
            margin-bottom: 6px;
        }

        .order-header-info p {
            font-size: 13px;
            color: #64748b;
        }

        .badges-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 750;
        }

        .status-completed { background: #dcfce7; color: #15803d; }
        .status-shipping { background: #e0e7ff; color: #3730a3; }
        .status-process { background: #ede9fe; color: #6d28d9; }
        .status-info { background: #e0f2fe; color: #0369a1; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        /* TIMELINE CARD */
        .timeline-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.03);
        }

        .timeline-card h2 {
            font-size: 17px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 24px;
        }

        .stepper-horizontal {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            position: relative;
            margin-bottom: 24px;
        }

        .stepper-horizontal::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #e2e8f0;
            z-index: 1;
        }

        .step-item {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .step-item.done .step-circle {
            background: #10b981;
            border-color: #10b981;
            color: #ffffff;
        }

        .step-item.active .step-circle {
            background: #087cff;
            border-color: #087cff;
            color: #ffffff;
            box-shadow: 0 0 0 5px rgba(8, 124, 255, 0.2);
        }

        .step-title {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }

        .step-item.active .step-title {
            color: #087cff;
        }

        .step-time {
            font-size: 11px;
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .stepper-horizontal {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .stepper-horizontal::before {
                display: none;
            }
            .step-item {
                flex-direction: row;
                text-align: left;
                gap: 14px;
            }
        }

        /* DETAIL GRID */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        .section-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.03);
        }

        .section-card h3 {
            font-size: 16px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .product-item-row {
            display: grid;
            grid-template-columns: 70px 1fr auto;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid #f8fafc;
            align-items: center;
        }

        .product-thumb-box {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-item-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            text-decoration: none;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            color: #64748b;
        }

        .info-row strong {
            color: #1e293b;
        }

        .history-list {
            list-style: none;
            padding: 0;
            margin: 0;
            position: relative;
        }

        .history-item {
            position: relative;
            padding-left: 24px;
            padding-bottom: 18px;
        }

        .history-item::before {
            content: '';
            position: absolute;
            left: 4px;
            top: 6px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .history-item:last-child::before {
            display: none;
        }

        .history-item::after {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #087cff;
        }

        .history-time {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .history-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .history-desc {
            font-size: 12px;
            color: #64748b;
        }

        .action-banner-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 18px;
            margin-top: 16px;
            text-align: center;
        }

        .btn-confirm-big {
            display: inline-block;
            width: 100%;
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 750;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-confirm-big:hover {
            background: #059669;
        }

        .btn-pay-big {
            display: inline-block;
            width: 100%;
            background: #087cff;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 750;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-pay-big:hover {
            background: #0066d6;
        }
    </style>
</head>
<body>

    <!-- NAVBAR USER -->
    @include('partials.navbar')

    <div class="page-container">

        <!-- NAV BACK -->
        <div class="back-nav">
            <a href="{{ route('user.orders', ['tab' => 'orders']) }}" class="back-link">
                Kembali ke Pesanan Saya
            </a>
        </div>

        <!-- ALERTS -->
        @if(session('success'))
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
                {{ session('error') }}
            </div>
        @endif

        @php
            $paymentStatusStr = strtolower((string) ($firstOrder->p_status ?? 'pending'));
            $orderStatusStr = strtolower((string) ($firstOrder->order_status ?? 'menunggu_pembayaran'));
            $isPaid = in_array($paymentStatusStr, ['completed', 'selesai', 'paid']);
            $isCancelled = in_array($paymentStatusStr, ['cancelled', 'batal']) || $orderStatusStr === 'dibatalkan';

            // Tahapan Stepper Tracker (1: Dibuat, 2: Terbayar, 3: Diproses, 4: Dikirim, 5: Selesai)
            if ($isCancelled) {
                $stepIndex = 0;
            } elseif ($orderStatusStr === 'selesai') {
                $stepIndex = 5;
            } elseif ($orderStatusStr === 'dikirim') {
                $stepIndex = 4;
            } elseif ($orderStatusStr === 'sedang_diproses') {
                $stepIndex = 3;
            } elseif ($isPaid || $orderStatusStr === 'pembayaran_berhasil') {
                $stepIndex = 2; // Terbayar / Menunggu Diproses
            } else {
                $stepIndex = 1; // Menunggu Pembayaran
            }
        @endphp

        <!-- HEADER PESANAN -->
        <div class="order-header-card">
            <div class="order-header-info">
                <h1>Detail & Pelacakan Pesanan</h1>
                <p>Nomor Pesanan: <strong>#{{ $trxId }}</strong> &bull; Dibuat pada: {{ date('d M Y, H:i', strtotime($firstOrder->created_at ?? now())) }}</p>
            </div>

            <div class="badges-group">
                {{-- Status Pembayaran Tunggal & Konsisten --}}
                @if($isPaid)
                    <span class="status-badge status-completed">Terbayar</span>
                @elseif($isCancelled)
                    <span class="status-badge status-cancelled">Dibatalkan</span>
                @else
                    <span class="status-badge status-pending">Menunggu Pembayaran</span>
                @endif

                {{-- Status Pemrosesan Pesanan (Ditampilkan setelah pembayaran berhasil) --}}
                @if($isPaid && !$isCancelled)
                    @if($orderStatusStr === 'sedang_diproses')
                        <span class="status-badge status-process">Sedang Diproses</span>
                    @elseif($orderStatusStr === 'dikirim')
                        <span class="status-badge status-shipping">Sedang Dikirim</span>
                    @elseif($orderStatusStr === 'selesai')
                        <span class="status-badge status-completed">Pesanan Selesai</span>
                    @else
                        <span class="status-badge status-info">Menunggu Diproses</span>
                    @endif
                @endif
            </div>
        </div>

        <!-- TIMELINE PELACAKAN PESANAN (5 STAGES) -->
        <div class="timeline-card">
            <h2>Pelacakan Status Pengiriman</h2>

            <div class="stepper-horizontal">
                <!-- STEP 1: Pesanan Dibuat -->
                <div class="step-item {{ $stepIndex > 1 ? 'done' : ($stepIndex === 1 ? 'active' : '') }}">
                    <div class="step-circle">1</div>
                    <div class="step-title">Pesanan Dibuat</div>
                    <div class="step-time">{{ date('d M, H:i', strtotime($firstOrder->created_at ?? now())) }}</div>
                </div>

                <!-- STEP 2: Terbayar -->
                <div class="step-item {{ $stepIndex > 2 ? 'done' : ($stepIndex === 2 ? 'active' : '') }}">
                    <div class="step-circle">2</div>
                    <div class="step-title">Terbayar</div>
                    <div class="step-time">
                        @if(!empty($firstOrder->paid_at))
                            {{ date('d M, H:i', strtotime($firstOrder->paid_at)) }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- STEP 3: Sedang Diproses -->
                <div class="step-item {{ $stepIndex > 3 ? 'done' : ($stepIndex === 3 ? 'active' : '') }}">
                    <div class="step-circle">3</div>
                    <div class="step-title">Sedang Diproses</div>
                    <div class="step-time">
                        @if(!empty($firstOrder->processed_at))
                            {{ date('d M, H:i', strtotime($firstOrder->processed_at)) }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- STEP 4: Dikirim -->
                <div class="step-item {{ $stepIndex > 4 ? 'done' : ($stepIndex === 4 ? 'active' : '') }}">
                    <div class="step-circle">4</div>
                    <div class="step-title">Sedang Dikirim</div>
                    <div class="step-time">
                        @if(!empty($firstOrder->shipped_at))
                            {{ date('d M, H:i', strtotime($firstOrder->shipped_at)) }}
                        @else
                            -
                        @endif
                    </div>
                </div>

                <!-- STEP 5: Selesai -->
                <div class="step-item {{ $stepIndex === 5 ? 'done' : '' }}">
                    <div class="step-circle">5</div>
                    <div class="step-title">Pesanan Selesai</div>
                    <div class="step-time">
                        @if(!empty($firstOrder->completed_at))
                            {{ date('d M, H:i', strtotime($firstOrder->completed_at)) }}
                        @else
                            -
                        @endif
                    </div>
                </div>
            </div>

            <!-- JIKA STATUS DIKIRIM: TAMPILKAN BANNER KONFIRMASI PENERIMAAN -->
            @if($orderStatusStr === 'dikirim')
                <div class="action-banner-box">
                    <h4 style="color: #1e40af; font-size: 15px; margin-bottom: 6px;">Barang Sudah Anda Terima?</h4>
                    <p style="color: #475569; font-size: 13px; margin-bottom: 14px;">
                        Kurir <strong>{{ $firstOrder->courier_name ?? 'Ekspedisi' }}</strong> dengan Resi <strong>{{ $firstOrder->tracking_number ?? '-' }}</strong> telah mengirimkan pesanan Anda.
                        Silakan klik tombol di bawah jika barang telah Anda terima dengan baik.
                    </p>
                    <form action="{{ route('user.orders.confirm-received', $trxId) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah menerima paket pesanan ini dengan kondisi baik?');">
                        @csrf
                        <button type="submit" class="btn-confirm-big">
                            Pesanan Diterima
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- DETAIL GRID (2 KOLOM) -->
        <div class="detail-grid">

            <!-- KOLOM KIRI -->
            <div>
                <!-- DAFTAR PRODUK -->
                <div class="section-card">
                    <h3>Daftar Produk Pesanan</h3>
                    @foreach($orders as $item)
                        <div class="product-item-row">
                            <div class="product-thumb-box">
                                @if(!empty($item->product_image))
                                    <img src="{{ asset('assets/images/' . $item->product_image) }}" alt="{{ $item->product_title }}">
                                @else
                                    <span style="font-size: 11px; color: #94a3b8;">No Image</span>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('products.show', $item->product_id) }}" class="product-item-title">
                                    {{ $item->product_title }}
                                </a>
                                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                    {{ $item->qty }} Barang &times; Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <strong style="color: #10245c; font-size: 14px;">
                                    Rp {{ number_format($item->total_amt ?: ($item->qty * $item->product_price), 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- INFORMASI PENGIRIMAN & PENERIMA -->
                <div class="section-card">
                    <h3>Informasi Pengiriman & Penerima</h3>
                    <div class="info-row">
                        <span>Nama Penerima:</span>
                        <strong>{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'Pelanggan' }}</strong>
                    </div>
                    <div class="info-row">
                        <span>Nomor Telepon:</span>
                        <strong>{{ $user->mobile ?? '-' }}</strong>
                    </div>
                    <div class="info-row">
                        <span>Alamat Pengiriman:</span>
                        <strong>{{ $user->address1 ?? '-' }}</strong>
                    </div>

                    @if(!empty($firstOrder->courier_name) || !empty($firstOrder->tracking_number))
                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                            <div class="info-row">
                                <span>Jasa Kurir:</span>
                                <strong style="color: #087cff;">{{ $firstOrder->courier_name ?? '-' }}</strong>
                            </div>
                            <div class="info-row">
                                <span>Nomor Resi:</span>
                                <strong style="color: #087cff; font-family: monospace;">{{ $firstOrder->tracking_number ?? '-' }}</strong>
                            </div>
                            @if(!empty($firstOrder->shipping_notes))
                                <div class="info-row">
                                    <span>Catatan Kurir:</span>
                                    <span>{{ $firstOrder->shipping_notes }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- RIWAYAT PERKEMBANGAN PESANAN -->
                @if($histories->isNotEmpty())
                    <div class="section-card">
                        <h3>Riwayat Perkembangan Pesanan</h3>
                        <ul class="history-list">
                            @foreach($histories as $h)
                                <li class="history-item">
                                    <div class="history-time">{{ date('d M Y, H:i', strtotime($h->created_at)) }}</div>
                                    <div class="history-title">{{ $h->title }}</div>
                                    @if(!empty($h->description))
                                        <div class="history-desc">{{ $h->description }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- KOLOM KANAN: RINGKASAN PEMBAYARAN -->
            <div>
                <div class="section-card">
                    <h3>Ringkasan Pembayaran</h3>

                    <div class="info-row">
                        <span>Metode Pembayaran:</span>
                        <strong>{{ $firstOrder->payment_method ?? 'Midtrans' }}</strong>
                    </div>

                    @if(!empty($firstOrder->midtrans_payment_type))
                        <div class="info-row">
                            <span>Tipe Pembayaran:</span>
                            <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $firstOrder->midtrans_payment_type) }}</strong>
                        </div>
                    @endif

                    @if(!empty($firstOrder->midtrans_transaction_id))
                        <div class="info-row">
                            <span>ID Transaksi:</span>
                            <span style="font-size: 11px; font-family: monospace;" title="{{ $firstOrder->midtrans_transaction_id }}">
                                {{ substr($firstOrder->midtrans_transaction_id, 0, 16) }}...
                            </span>
                        </div>
                    @endif

                    <div class="info-row">
                        <span>Status Pembayaran:</span>
                        @if($isPaid)
                            <strong style="color: #15803d;">Terbayar (Lunas)</strong>
                        @elseif($isCancelled)
                            <strong style="color: #b91c1c;">Dibatalkan</strong>
                        @else
                            <strong style="color: #b45309;">Menunggu Pembayaran</strong>
                        @endif
                    </div>

                    @if(!empty($firstOrder->paid_at))
                        <div class="info-row">
                            <span>Waktu Pembayaran:</span>
                            <strong>{{ date('d M Y, H:i', strtotime($firstOrder->paid_at)) }}</strong>
                        </div>
                    @endif

                    <div style="margin: 16px 0; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                        <div class="info-row" style="font-size: 16px;">
                            <span>Total Pembayaran:</span>
                            <strong style="color: #087cff; font-size: 18px;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if(!$isPaid && !$isCancelled && $orderStatusStr === 'menunggu_pembayaran' && strtolower($firstOrder->payment_method ?? '') === 'midtrans')
                        <a href="{{ route('user.payment', $trxId) }}" class="btn-pay-big">
                            Bayar Sekarang via Midtrans
                        </a>
                    @endif
                </div>
            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    @include('partials.chatbot')

</body>
</html>
