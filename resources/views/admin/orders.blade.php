<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <style>
        .stats-grid-6 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .order-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            border-left: 4px solid #cbd5e1;
        }

        .order-stat-box.blue { border-left-color: #087cff; }
        .order-stat-box.amber { border-left-color: #f59e0b; }
        .order-stat-box.cyan { border-left-color: #06b6d4; }
        .order-stat-box.purple { border-left-color: #8b5cf6; }
        .order-stat-box.indigo { border-left-color: #6366f1; }
        .order-stat-box.green { border-left-color: #10b981; }

        .order-stat-box p {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .order-stat-box h3 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        /* FILTER FORM */
        .filter-bar-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
        }

        .filter-form-flex {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            width: 100%;
        }

        .filter-input-text {
            flex: 2;
            min-width: 220px;
            padding: 9px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
        }

        .filter-select {
            flex: 1;
            min-width: 170px;
            padding: 9px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            background: #ffffff;
        }

        .btn-filter-submit {
            background: #087cff;
            color: #ffffff;
            border: none;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-filter-submit:hover {
            background: #0066d6;
        }

        .btn-filter-reset {
            background: #f1f5f9;
            color: #475569;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-filter-reset:hover {
            background: #e2e8f0;
        }

        /* BADGES */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 16px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-ready { background: #e0f2fe; color: #0369a1; }
        .badge-process { background: #ede9fe; color: #6d28d9; }
        .badge-shipping { background: #e0e7ff; color: #3730a3; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }

        /* ACTION BUTTONS */
        .btn-action-primary {
            background: #087cff;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-action-primary:hover {
            background: #0066d6;
        }

        .btn-action-success {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }

        .btn-action-success:hover {
            background: #059669;
        }

        .btn-action-light {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .btn-action-light:hover {
            background: #e2e8f0;
            color: #087cff;
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

        .modal-header h3 {
            margin: 0;
            font-size: 17px;
            font-weight: 750;
            color: #10245c;
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

    <!-- =====================================================
         SIDEBAR ADMIN
         ===================================================== -->
    @include('admin.partials.sidebar')

    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="admin-header">
            <div>
                <h1>Manajemen Pesanan</h1>
                <p>Kelola alur pemesanan, verifikasi Midtrans, pengiriman kurir, hingga penyelesaian pesanan</p>
            </div>

            <div class="admin-profile">
                <div class="profile-avatar">A</div>
                <div>
                    <strong>{{ session('admin_name') ?? 'Admin' }}</strong>
                    <small>Administrator</small>
                </div>
            </div>
        </header>

        <!-- STATS CARDS (6 STATUS) -->
        <div class="stats-grid-6">
            <div class="order-stat-box blue">
                <p>Total Pesanan</p>
                <h3>{{ $stats['total'] }}</h3>
            </div>
            <div class="order-stat-box amber">
                <p>Menunggu Pembayaran</p>
                <h3>{{ $stats['menunggu_pembayaran'] }}</h3>
            </div>
            <div class="order-stat-box cyan">
                <p>Menunggu Diproses</p>
                <h3>{{ $stats['pembayaran_berhasil'] }}</h3>
            </div>
            <div class="order-stat-box purple">
                <p>Sedang Diproses</p>
                <h3>{{ $stats['sedang_diproses'] }}</h3>
            </div>
            <div class="order-stat-box indigo">
                <p>Sedang Dikirim</p>
                <h3>{{ $stats['dikirim'] }}</h3>
            </div>
            <div class="order-stat-box green">
                <p>Pesanan Selesai</p>
                <h3>{{ $stats['selesai'] }}</h3>
            </div>
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

        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FILTER & SEARCH BAR -->
        <div class="filter-bar-container">
            <form action="{{ route('admin.orders') }}" method="GET" class="filter-form-flex">
                <input
                    type="text"
                    name="q"
                    class="filter-input-text"
                    placeholder="Cari No. Pesanan, Resi, Nama Pembeli, Email..."
                    value="{{ $search }}"
                >

                <select name="payment_status" class="filter-select">
                    <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>-- Status Pembayaran --</option>
                    <option value="Pending" {{ $paymentStatus === 'Pending' ? 'selected' : '' }}>Pending (Menunggu Bayar)</option>
                    <option value="Completed" {{ $paymentStatus === 'Completed' ? 'selected' : '' }}>Completed (Lunas)</option>
                    <option value="Cancelled" {{ $paymentStatus === 'Cancelled' ? 'selected' : '' }}>Cancelled (Batal)</option>
                </select>

                <select name="order_status" class="filter-select">
                    <option value="all" {{ $orderStatus === 'all' ? 'selected' : '' }}>-- Status Pemesanan --</option>
                    <option value="menunggu_pembayaran" {{ $orderStatus === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="pembayaran_berhasil" {{ $orderStatus === 'pembayaran_berhasil' ? 'selected' : '' }}>Pembayaran Berhasil (Siap Diproses)</option>
                    <option value="sedang_diproses" {{ $orderStatus === 'sedang_diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                    <option value="dikirim" {{ $orderStatus === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="selesai" {{ $orderStatus === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ $orderStatus === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <button type="submit" class="btn-filter-submit">
                    Filter
                </button>

                @if(!empty($search) || ($paymentStatus && $paymentStatus !== 'all') || ($orderStatus && $orderStatus !== 'all'))
                    <a href="{{ route('admin.orders') }}" class="btn-filter-reset">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- TABLE CARD -->
        <div class="dashboard-card">
            <div class="card-header">
                <div>
                    <h2>Daftar Pesanan Masuk</h2>
                    <p>Kelola seluruh tahapan proses pesanan pelanggan NexTech secara terintegrasi</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Pesanan & Waktu</th>
                            <th>Pelanggan</th>
                            <th>Produk</th>
                            <th>Total Bayar</th>
                            <th>Status Pembayaran</th>
                            <th>Status Pesanan</th>
                            <th>Kurir & Resi</th>
                            <th>Aksi Operasional</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $index => $order)
                        @php
                            $orderAmt = (int) ($order->total_amt ?: ($order->qty * ($order->product_price ?? 0)));
                            $rawOrderStatus = strtolower($order->order_status ?? 'menunggu_pembayaran');
                            $rawPaymentStatus = strtolower($order->p_status ?? 'pending');
                        @endphp
                        <tr>
                            <!-- 1. NO -->
                            <td>{{ $index + 1 }}</td>

                            <!-- 2. NO PESANAN & WAKTU -->
                            <td>
                                <strong style="color: #10245c; font-size: 13px;">#{{ $order->trx_id ?? ('ORD-' . $order->order_id) }}</strong>
                                <div style="font-size: 11px; color: #94a3b8;">{{ date('d/m/Y H:i', strtotime($order->created_at ?? now())) }}</div>
                                <div style="font-size: 10px; color: #64748b;">ID Item: #{{ $order->order_id }}</div>
                            </td>

                            <!-- 3. PELANGGAN -->
                            <td>
                                <strong>{{ trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')) ?: 'User #' . $order->user_id }}</strong>
                                <div style="font-size: 11px; color: #64748b;">{{ $order->customer_email ?? '-' }}</div>
                                <div style="font-size: 11px; color: #94a3b8;">{{ $order->customer_mobile ?? '-' }}</div>
                            </td>

                            <!-- 4. PRODUK -->
                            <td>
                                <span class="product-name" style="font-size: 13px;">{{ $order->product_title ?? ('Produk #' . $order->product_id) }}</span>
                                <div style="font-size: 12px; color: #64748b;">{{ $order->qty }} Unit &times; Rp {{ number_format($order->product_price ?? 0, 0, ',', '.') }}</div>
                            </td>

                            <!-- 5. TOTAL BAYAR -->
                            <td>
                                <strong style="color: #087cff; font-size: 14px;">Rp {{ number_format($orderAmt, 0, ',', '.') }}</strong>
                                <div style="font-size: 11px; color: #64748b;">{{ $order->payment_method ?? 'Midtrans' }}</div>
                            </td>

                            <!-- 6. STATUS PEMBAYARAN (MIDTRANS VERIFIKASI) -->
                            <td>
                                @if($rawPaymentStatus === 'completed' || $rawPaymentStatus === 'selesai' || $rawPaymentStatus === 'paid')
                                    <span class="badge badge-success">Terbayar</span>
                                    @if(!empty($order->midtrans_payment_type))
                                        <div style="font-size: 10px; color: #475569; text-transform: uppercase;">{{ $order->midtrans_payment_type }}</div>
                                    @endif
                                @elseif($rawPaymentStatus === 'cancelled')
                                    <span class="badge badge-danger">Batal</span>
                                @else
                                    <span class="badge badge-pending">Menunggu Pembayaran</span>
                                @endif

                                @if(!empty($order->midtrans_transaction_id))
                                    <div style="font-size: 9px; color: #94a3b8; font-family: monospace;" title="{{ $order->midtrans_transaction_id }}">
                                        {{ substr($order->midtrans_transaction_id, 0, 10) }}...
                                    </div>
                                @endif
                            </td>

                            <!-- 7. STATUS PESANAN -->
                            <td>
                                @if($rawOrderStatus === 'pembayaran_berhasil')
                                    <span class="badge badge-ready">Menunggu Diproses</span>
                                @elseif($rawOrderStatus === 'sedang_diproses')
                                    <span class="badge badge-process">Sedang Diproses</span>
                                @elseif($rawOrderStatus === 'dikirim')
                                    <span class="badge badge-shipping">Dikirim</span>
                                @elseif($rawOrderStatus === 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($rawOrderStatus === 'dibatalkan')
                                    <span class="badge badge-danger">Dibatalkan</span>
                                @else
                                    <span class="badge badge-pending">Menunggu Pembayaran</span>
                                @endif
                            </td>

                            <!-- 8. KURIR & RESI -->
                            <td>
                                @if(!empty($order->courier_name) || !empty($order->tracking_number))
                                    <div style="font-size: 12px; font-weight: 700; color: #1e293b;">{{ $order->courier_name ?? '-' }}</div>
                                    <div style="font-size: 11px; font-family: monospace; color: #087cff;">{{ $order->tracking_number ?? '-' }}</div>
                                @else
                                    <span style="font-size: 12px; color: #94a3b8;">-</span>
                                @endif
                            </td>

                            <!-- 9. AKSI OPERASIONAL -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                                    {{-- Tahap 1: Pembayaran Berhasil -> Tombol Proses Pesanan --}}
                                    @if($rawOrderStatus === 'pembayaran_berhasil')
                                        <form action="{{ route('admin.orders.process', $order->trx_id) }}" method="POST" onsubmit="return confirm('Mulai proses pesanan #{{ $order->trx_id }}?');">
                                            @csrf
                                            <button type="submit" class="btn-action-primary">
                                                Proses Pesanan
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Tahap 2: Sedang Diproses -> Tombol Kirim Pesanan --}}
                                    @if($rawOrderStatus === 'sedang_diproses')
                                        <button
                                            type="button"
                                            class="btn-action-success"
                                            onclick="openShipModal('{{ $order->trx_id }}')"
                                        >
                                            Kirim Pesanan
                                        </button>
                                    @endif

                                    {{-- Jika Menunggu Pembayaran --}}
                                    @if($rawOrderStatus === 'menunggu_pembayaran')
                                        <small style="color: #94a3b8; font-style: italic;">Menunggu Pembayaran</small>
                                    @endif

                                    {{-- Jika Sudah Dikirim --}}
                                    @if($rawOrderStatus === 'dikirim')
                                        <small style="color: #6366f1; font-weight: 600;">Menunggu Pembeli</small>
                                    @endif

                                    {{-- Tombol Detail & Riwayat --}}
                                    <div style="display: flex; gap: 4px; align-items: center; margin-top: 4px;">
                                        <a href="{{ route('admin.orders.detail', $order->trx_id) }}" class="btn-action-light" title="Lihat Detail & Timeline">
                                            Detail & Riwayat
                                        </a>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('admin.orders.destroy', $order->order_id) }}" method="POST" onsubmit="return confirm('Hapus pesanan ini dari riwayat?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; font-size: 13px;" title="Hapus">
                                                <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty">
                                <div class="empty-icon">
                                    <i data-lucide="shopping-bag" style="width: 36px; height: 36px; color: #94a3b8;"></i>
                                </div>
                                <h3>Tidak ada data pesanan</h3>
                                <p>Tidak ditemukan pesanan dengan kriteria pencarian dan filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-info">
                Menampilkan {{ $orders->count() }} data pesanan
            </div>
        </div>

    </main>

</div>

<!-- =====================================================
     MODAL KIRIM PESANAN
     ===================================================== -->
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

    // Close on outside click
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('shipModal');
        if (e.target === modal) {
            closeShipModal();
        }
    });
</script>

</body>
</html>
