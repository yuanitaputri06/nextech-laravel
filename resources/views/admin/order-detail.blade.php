<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #{{ $actualTrxId }} - Admin NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <style>
        .detail-page-container {
            padding: 24px;
        }

        .back-nav-bar {
            margin-bottom: 20px;
        }

        .back-link-admin {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #087cff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .back-link-admin:hover {
            text-decoration: underline;
        }

        .detail-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px 26px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title-box h1 {
            font-size: 20px;
            font-weight: 800;
            color: #10245c;
            margin: 0 0 4px 0;
        }

        .header-title-box p {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }

        .grid-detail-admin {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 950px) {
            .grid-detail-admin {
                grid-template-columns: 1fr;
            }
        }

        .admin-card-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 24px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .admin-card-box h3 {
            font-size: 16px;
            font-weight: 750;
            color: #10245c;
            margin: 0 0 16px 0;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-pair {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            color: #64748b;
        }

        .info-pair strong {
            color: #0f172a;
        }

        /* TIMELINE STYLES */
        .timeline-admin-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .timeline-admin-item {
            position: relative;
            padding-left: 24px;
            padding-bottom: 18px;
        }

        .timeline-admin-item::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 6px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-admin-item:last-child::before {
            display: none;
        }

        .timeline-admin-item::after {
            content: '';
            position: absolute;
            left: 1px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #087cff;
        }

        .timeline-admin-time {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 2px;
        }

        .timeline-admin-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .timeline-admin-desc {
            font-size: 12px;
            color: #64748b;
        }

        .badge-actor {
            display: inline-block;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            margin-top: 4px;
        }

        /* MODAL */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .modal-box {
            background: #ffffff;
            width: 90%;
            max-width: 480px;
            border-radius: 14px;
            padding: 26px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #94a3b8;
        }

        .form-group-modal {
            margin-bottom: 14px;
            text-align: left;
        }

        .form-group-modal label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-group-modal input,
        .form-group-modal select,
        .form-group-modal textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
    @include('admin.partials.sidebar')

    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- NAV BACK -->
        <div class="back-nav-bar">
            <a href="{{ route('admin.orders') }}" class="back-link-admin">
                Kembali ke Daftar Pesanan
            </a>
        </div>

        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        @php
            $rawOrderStatus = strtolower($firstOrder->order_status ?? 'menunggu_pembayaran');
            $rawPaymentStatus = strtolower($firstOrder->p_status ?? 'pending');
        @endphp

        <!-- HEADER DETAIL -->
        <div class="detail-header-card">
            <div class="header-title-box">
                <h1>Detail Pesanan #{{ $actualTrxId }}</h1>
                <p>Waktu Pemesanan: {{ date('d M Y, H:i', strtotime($firstOrder->created_at ?? now())) }} &bull; Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                {{-- Status Pembayaran --}}
                @if($rawPaymentStatus === 'completed' || $rawPaymentStatus === 'selesai' || $rawPaymentStatus === 'paid')
                    <span style="background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Terbayar
                    </span>
                @elseif($rawPaymentStatus === 'cancelled')
                    <span style="background: #fee2e2; color: #b91c1c; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Pembayaran Dibatalkan
                    </span>
                @else
                    <span style="background: #fef3c7; color: #b45309; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Menunggu Pembayaran
                    </span>
                @endif

                {{-- Status Pesanan --}}
                @if($rawOrderStatus === 'pembayaran_berhasil')
                    <span style="background: #e0f2fe; color: #0369a1; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Menunggu Diproses
                    </span>
                @elseif($rawOrderStatus === 'sedang_diproses')
                    <span style="background: #ede9fe; color: #6d28d9; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Sedang Diproses
                    </span>
                @elseif($rawOrderStatus === 'dikirim')
                    <span style="background: #e0e7ff; color: #3730a3; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Sedang Dikirim
                    </span>
                @elseif($rawOrderStatus === 'selesai')
                    <span style="background: #dcfce7; color: #15803d; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Pesanan Selesai
                    </span>
                @elseif($rawOrderStatus === 'dibatalkan')
                    <span style="background: #fee2e2; color: #b91c1c; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Dibatalkan
                    </span>
                @else
                    <span style="background: #fef3c7; color: #b45309; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 750;">
                        Menunggu Pembayaran
                    </span>
                @endif
            </div>
        </div>

        <!-- OPERATIONAL ACTION BUTTONS FOR ADMIN -->
        @if($rawOrderStatus === 'pembayaran_berhasil')
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #1e40af; font-size: 15px;">Pesanan Sudah Terbayar dan Siap Diproses</h4>
                    <p style="margin: 0; font-size: 13px; color: #475569;">Klik tombol proses untuk mengubah status menjadi <strong>Sedang Diproses</strong>.</p>
                </div>
                <form action="{{ route('admin.orders.process', $actualTrxId) }}" method="POST" onsubmit="return confirm('Mulai proses pesanan #{{ $actualTrxId }}?');">
                    @csrf
                    <button type="submit" style="background: #087cff; color: #ffffff; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 750; cursor: pointer;">
                        Proses Pesanan Sekarang
                    </button>
                </form>
            </div>
        @elseif($rawOrderStatus === 'sedang_diproses')
            <div style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 12px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div>
                    <h4 style="margin: 0 0 4px 0; color: #5b21b6; font-size: 15px;">Pesanan Sedang Diproses dan Siap Dikirim</h4>
                    <p style="margin: 0; font-size: 13px; color: #475569;">Masukkan nomor resi dan kurir untuk mengubah status menjadi <strong>Dikirim</strong>.</p>
                </div>
                <button type="button" onclick="openShipModal('{{ $actualTrxId }}')" style="background: #10b981; color: #ffffff; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 750; cursor: pointer;">
                    Kirim Pesanan (Input Resi)
                </button>
            </div>
        @endif

        <!-- GRID DETAIL ADMIN -->
        <div class="grid-detail-admin">

            <!-- KOLOM UTAMA (KIRI) -->
            <div>
                <!-- DAFTAR ITEM PRODUK -->
                <div class="admin-card-box">
                    <h3>Daftar Produk Pesanan ({{ $orders->count() }} Item)</h3>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Harga Satuan</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_title ?? ('Produk #' . $item->product_id) }}</strong>
                                        <div style="font-size: 11px; color: #94a3b8;">ID: {{ $item->product_id }}</div>
                                    </td>
                                    <td>Rp {{ number_format($item->product_price ?? 0, 0, ',', '.') }}</td>
                                    <td><strong>{{ $item->qty }}</strong></td>
                                    <td><strong style="color: #087cff;">Rp {{ number_format($item->total_amt ?: ($item->qty * ($item->product_price ?? 0)), 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- RIWAYAT PERUBAHAN STATUS PESANAN -->
                <div class="admin-card-box">
                    <h3>Riwayat Perubahan Status Pesanan</h3>
                    @if($histories->isEmpty())
                        <p style="color: #94a3b8; font-size: 13px;">Belum ada catatan riwayat perubahan status.</p>
                    @else
                        <ul class="timeline-admin-list">
                            @foreach($histories as $h)
                                <li class="timeline-admin-item">
                                    <div class="timeline-admin-time">{{ date('d M Y, H:i:s', strtotime($h->created_at)) }}</div>
                                    <div class="timeline-admin-title">{{ $h->title }}</div>
                                    @if(!empty($h->description))
                                        <div class="timeline-admin-desc">{{ $h->description }}</div>
                                    @endif
                                    <span class="badge-actor">Oleh: {{ $h->actor_name ?? ucfirst($h->actor_type) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- SIDEBAR INFORMASI (KANAN) -->
            <div>
                <!-- DATA PELANGGAN & PENGIRIMAN -->
                <div class="admin-card-box">
                    <h3>Informasi Pelanggan & Alamat</h3>
                    <div class="info-pair">
                        <span>Nama Pembeli:</span>
                        <strong>{{ trim(($firstOrder->first_name ?? '') . ' ' . ($firstOrder->last_name ?? '')) ?: 'User #' . $firstOrder->user_id }}</strong>
                    </div>
                    <div class="info-pair">
                        <span>Email:</span>
                        <strong>{{ $firstOrder->customer_email ?? '-' }}</strong>
                    </div>
                    <div class="info-pair">
                        <span>Telepon:</span>
                        <strong>{{ $firstOrder->customer_mobile ?? '-' }}</strong>
                    </div>
                    <div class="info-pair">
                        <span>Alamat Lengkap:</span>
                        <span style="max-width: 60%; text-align: right; color: #1e293b;">{{ $firstOrder->customer_address ?? '-' }}</span>
                    </div>

                    @if(!empty($firstOrder->courier_name) || !empty($firstOrder->tracking_number))
                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                            <div class="info-pair">
                                <span>Kurir Pengiriman:</span>
                                <strong style="color: #087cff;">{{ $firstOrder->courier_name ?? '-' }}</strong>
                            </div>
                            <div class="info-pair">
                                <span>Nomor Resi:</span>
                                <strong style="color: #087cff; font-family: monospace;">{{ $firstOrder->tracking_number ?? '-' }}</strong>
                            </div>
                            @if(!empty($firstOrder->shipping_notes))
                                <div class="info-pair">
                                    <span>Catatan Kurir:</span>
                                    <span>{{ $firstOrder->shipping_notes }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- DETAIL PEMBAYARAN MIDTRANS -->
                <div class="admin-card-box">
                    <h3>Detail Pembayaran</h3>
                    <div class="info-pair">
                        <span>Metode:</span>
                        <strong>{{ $firstOrder->payment_method ?? 'Midtrans' }}</strong>
                    </div>
                    @if(!empty($firstOrder->midtrans_payment_type))
                        <div class="info-pair">
                            <span>Tipe Midtrans:</span>
                            <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $firstOrder->midtrans_payment_type) }}</strong>
                        </div>
                    @endif
                    @if(!empty($firstOrder->midtrans_status))
                        <div class="info-pair">
                            <span>Status Midtrans:</span>
                            <span style="font-weight: 700;">{{ $firstOrder->midtrans_status }}</span>
                        </div>
                    @endif
                    @if(!empty($firstOrder->midtrans_transaction_id))
                        <div class="info-pair">
                            <span>ID Transaksi:</span>
                            <span style="font-size: 11px; font-family: monospace;" title="{{ $firstOrder->midtrans_transaction_id }}">
                                {{ $firstOrder->midtrans_transaction_id }}
                            </span>
                        </div>
                    @endif
                    @if(!empty($firstOrder->paid_at))
                        <div class="info-pair">
                            <span>Waktu Lunas:</span>
                            <strong>{{ date('d M Y, H:i', strtotime($firstOrder->paid_at)) }}</strong>
                        </div>
                    @endif

                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                        <div class="info-pair" style="font-size: 15px;">
                            <span>Total Tagihan:</span>
                            <strong style="color: #087cff; font-size: 18px;">Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </main>

</div>

<!-- MODAL KIRIM PESANAN -->
<div id="shipModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Kirim Pesanan</h3>
            <button type="button" class="modal-close" onclick="closeShipModal()">&times;</button>
        </div>

        <form id="shipForm" method="POST">
            @csrf
            <div class="form-group-modal">
                <label>Nomor Pesanan</label>
                <input type="text" id="modalTrxId" readonly style="background: #f8fafc; font-weight: 700; color: #10245c;">
            </div>

            <div class="form-group-modal">
                <label for="courier_name">Nama Jasa Kurir <span style="color: red;">*</span></label>
                <select name="courier_name" id="courier_name" required>
                    <option value="">-- Pilih Ekspedisi Kurir --</option>
                    <option value="JNE Regular">JNE Regular</option>
                    <option value="J&T Express">J&T Express</option>
                    <option value="SiCepat REG">SiCepat REG</option>
                    <option value="Anteraja">Anteraja</option>
                    <option value="POS Indonesia">POS Indonesia</option>
                    <option value="GoSend / GrabExpress">GoSend / GrabExpress (Instant)</option>
                    <option value="Kurir Toko NexTech">Kurir Internal NexTech</option>
                </select>
            </div>

            <div class="form-group-modal">
                <label for="tracking_number">Nomor Resi Pengiriman <span style="color: red;">*</span></label>
                <input type="text" name="tracking_number" id="tracking_number" placeholder="Contoh: NX-JNE-88291024" required>
            </div>

            <div class="form-group-modal">
                <label for="shipping_notes">Catatan Pengiriman (Opsional)</label>
                <textarea name="shipping_notes" id="shipping_notes" rows="2" placeholder="Catatan kemasan, estimasi tiba, dll..."></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" class="btn-filter-reset" onclick="closeShipModal()">Batal</button>
                <button type="submit" class="btn-action-primary" style="padding: 9px 20px;">Simpan & Kirim Pesanan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openShipModal(trxId) {
        const modal = document.getElementById('shipModal');
        const form = document.getElementById('shipForm');
        const trxIdInput = document.getElementById('modalTrxId');

        trxIdInput.value = '#' + trxId;
        form.action = '/admin/orders/' + encodeURIComponent(trxId) + '/ship';
        modal.style.display = 'flex';
    }

    function closeShipModal() {
        document.getElementById('shipModal').style.display = 'none';
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('shipModal');
        if (e.target === modal) {
            closeShipModal();
        }
    });
</script>

</body>
</html>
