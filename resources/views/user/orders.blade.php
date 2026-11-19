<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexTech - Pesanan Saya</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .page-container {
            width: 90%;
            max-width: 1200px;
            margin: 36px auto 60px;
            min-height: 60vh;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 26px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 6px;
        }

        .page-header p {
            font-size: 14px;
            color: #64748b;
        }

        /* TABS */
        .tabs-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 28px;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            font-size: 15px;
            font-weight: 600;
            color: #64748b;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: #087cff;
        }

        .tab-btn.active {
            color: #087cff;
            border-bottom-color: #087cff;
        }

        .tab-count {
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 700;
        }

        .tab-btn.active .tab-count {
            background: #dbeafe;
            color: #087cff;
        }

        /* ALERT NOTIFICATION */
        .alert-box {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* CART LAYOUT */
        .cart-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
        }

        .cart-items-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.05), 0 1px 3px rgba(16, 36, 92, 0.03);
        }

        .cart-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .cart-select-all {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .cart-select-all input[type="checkbox"],
        .cart-item-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #087cff;
        }

        .cart-item-row {
            display: grid;
            grid-template-columns: auto 90px 1fr auto;
            gap: 18px;
            padding: 20px;
            border-bottom: 1px solid #f1f5f9;
            align-items: center;
        }

        .cart-item-row:last-child {
            border-bottom: none;
        }

        .cart-item-thumb {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-item-thumb img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .cart-item-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 4px;
        }

        .cart-item-info h4 a {
            color: inherit;
            text-decoration: none;
        }

        .cart-item-info h4 a:hover {
            color: #087cff;
        }

        .cart-item-cat {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .cart-item-price {
            font-size: 14px;
            font-weight: 700;
            color: #087cff;
        }

        .cart-item-stock-tag {
            font-size: 11px;
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 4px;
        }

        .stock-available {
            background: #dcfce7;
            color: #15803d;
        }

        .stock-limited {
            background: #fef3c7;
            color: #b45309;
        }

        .stock-out {
            background: #fee2e2;
            color: #b91c1c;
        }

        .cart-item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 14px;
        }

        .qty-control {
            display: inline-flex;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            overflow: hidden;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }

        .qty-btn:hover {
            background: #e2e8f0;
        }

        .qty-input {
            width: 44px;
            height: 32px;
            border: none;
            background: #ffffff;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .qty-input:focus {
            outline: none;
        }

        .delete-item-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s;
            padding: 0;
        }

        .delete-item-btn:hover {
            color: #ef4444;
        }

        /* CART SUMMARY CARD */
        .cart-summary-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.07), 0 2px 6px rgba(16, 36, 92, 0.03);
            position: sticky;
            top: 96px;
        }

        .summary-title {
            font-size: 17px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .summary-row.total-row {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #cbd5e1;
            font-size: 16px;
            font-weight: 750;
            color: #10245c;
        }

        .summary-row.total-row .total-price {
            font-size: 20px;
            color: #087cff;
        }

        .checkout-submit-btn {
            width: 100%;
            padding: 13px;
            background: #087cff;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.3);
            transition: all 0.2s ease;
            margin-top: 18px;
        }

        .checkout-submit-btn:hover {
            background: #066bdc;
            box-shadow: 0 6px 18px rgba(8, 124, 255, 0.4);
            transform: translateY(-1px);
        }

        .checkout-submit-btn:disabled {
            background: #cbd5e1;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }

        /* ORDERS LIST LAYOUT */
        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .order-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.03);
            transition: box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .order-card:hover {
            box-shadow: 0 8px 24px rgba(8, 124, 255, 0.11), 0 2px 6px rgba(16, 36, 92, 0.05);
            border-color: #b9d7fb;
        }

        .order-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: #64748b;
        }

        .order-meta strong {
            color: #10245c;
            font-weight: 700;
        }

        .order-status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-completed {
            background: #dcfce7;
            color: #15803d;
        }

        .status-shipping {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-process {
            background: #ede9fe;
            color: #6d28d9;
        }

        .status-info {
            background: #e0f2fe;
            color: #0369a1;
        }

        .status-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* MINI TRACKER STEPPER */
        .order-mini-tracker {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            margin: 14px 0 16px;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .tracker-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .tracker-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #cbd5e1;
            margin-bottom: 6px;
            transition: all 0.2s ease;
        }

        .tracker-step.done .tracker-dot {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .tracker-step.active .tracker-dot {
            background: #087cff;
            box-shadow: 0 0 0 4px rgba(8, 124, 255, 0.25);
        }

        .tracker-label {
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
        }

        .tracker-step.done .tracker-label {
            color: #0f172a;
        }

        .tracker-step.active .tracker-label {
            color: #087cff;
            font-weight: 750;
        }

        .shipping-info-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 13px;
            color: #1e40af;
        }

        .btn-confirm-received {
            background: #10b981;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-confirm-received:hover {
            background: #059669;
        }

        .btn-detail-order {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 650;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-detail-order:hover {
            background: #e2e8f0;
            color: #087cff;
        }

        .order-card-body {
            display: grid;
            grid-template-columns: 80px 1fr auto;
            gap: 18px;
            align-items: center;
        }

        @media (max-width: 650px) {
            .order-card-body {
                grid-template-columns: 70px 1fr;
            }
            .order-card-total {
                grid-column: 1 / -1;
                text-align: right;
                border-top: 1px solid #f1f5f9;
                padding-top: 10px;
            }
        }

        .order-thumb {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-thumb img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .order-product-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 4px;
        }

        .order-product-info p {
            font-size: 13px;
            color: #64748b;
        }

        .order-card-total {
            text-align: right;
        }

        .order-card-total .total-label {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 2px;
        }

        .order-card-total .total-val {
            font-size: 17px;
            font-weight: 750;
            color: #087cff;
        }

        .order-card-total .order-detail-link {
            display: inline-block;
            margin-top: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #087cff;
            text-decoration: underline;
        }

        /* EMPTY STATE */
        .empty-state {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state-icon {
            font-size: 54px;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 20px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    @include('partials.navbar')

    <div class="page-container">
        <div class="page-header">
            <h1>Pesanan Saya</h1>
            <p>Kelola keranjang belanja dan pantau riwayat transaksi belanja teknologi Anda.</p>
        </div>

        @if(session('success'))
            <div class="alert-box alert-success" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="circle-check" style="width: 16px; height: 16px;"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-error" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="circle-x" style="width: 16px; height: 16px;"></i>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
            </div>
        @endif

        <!-- TABS NAVIGASI -->
        <div class="tabs-nav">
            <a href="{{ route('user.orders', ['tab' => 'cart']) }}" class="tab-btn {{ $activeTab === 'cart' ? 'active' : '' }}">
                <i data-lucide="shopping-cart" style="width: 16px; height: 16px; vertical-align: -2px; margin-right: 4px;"></i> Keranjang
                <span class="tab-count">{{ $cartItems->count() }}</span>
            </a>
            <a href="{{ route('user.orders', ['tab' => 'orders']) }}" class="tab-btn {{ $activeTab === 'orders' ? 'active' : '' }}">
                <i data-lucide="package" style="width: 16px; height: 16px; vertical-align: -2px; margin-right: 4px;"></i> Riwayat Pesanan
                <span class="tab-count">{{ $orders->count() }}</span>
            </a>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: KERANJANG BELANJA                       -->
        <!-- ============================================== -->
        @if ($activeTab === 'cart')
            @if ($cartItems->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i data-lucide="shopping-cart" style="width: 48px; height: 48px; color: #94a3b8;"></i>
                    </div>
                    <h3>Keranjang Belanja Masih Kosong</h3>
                    <p>Yuk, jelajahi katalog produk teknologi terbaik kami dan tambahkan gadget impianmu ke keranjang!</p>
                    <a href="{{ route('products.index') }}" class="buy-button" style="display: inline-block; width: auto; padding: 12px 28px;">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <form action="{{ route('checkout') }}" method="POST" id="cartForm">
                    @csrf
                    <div class="cart-layout">
                        <!-- DAFTAR ITEM KERANJANG -->
                        <div class="cart-items-card">
                            <div class="cart-header-bar">
                                <label class="cart-select-all">
                                    <input type="checkbox" id="selectAllCheckbox" checked onchange="toggleSelectAll(this)">
                                    <span>Pilih Semua ({{ $cartItems->count() }} Produk)</span>
                                </label>
                                <span>Subtotal</span>
                            </div>

                            @foreach ($cartItems as $item)
                                @php
                                    $itemStock = (int) ($item->stock ?? 0);
                                    $itemSubtotal = $item->qty * (int) $item->product_price;
                                @endphp
                                <div class="cart-item-row" id="cartRow-{{ $item->cart_id }}">
                                    <!-- CHECKBOX -->
                                    <div class="cart-item-checkbox">
                                        <input
                                            type="checkbox"
                                            name="selected_items[]"
                                            value="{{ $item->cart_id }}"
                                            class="item-check"
                                            data-cart-id="{{ $item->cart_id }}"
                                            data-price="{{ (int) $item->product_price }}"
                                            data-qty="{{ (int) $item->qty }}"
                                            checked
                                            onchange="updateCartTotals()"
                                            {{ $itemStock <= 0 ? 'disabled' : '' }}
                                        >
                                    </div>

                                    <!-- THUMBNAIL -->
                                    <div class="cart-item-thumb">
                                        @if (!empty($item->product_image))
                                            <img src="{{ asset('assets/images/' . $item->product_image) }}" alt="{{ $item->product_title }}">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    </div>

                                    <!-- INFO -->
                                    <div class="cart-item-info">
                                        <h4>
                                            <a href="{{ route('products.show', $item->product_id) }}">
                                                {{ $item->product_title }}
                                            </a>
                                        </h4>
                                        <div class="cart-item-cat">{{ $item->cat_title ?? 'Kategori Produk' }}</div>
                                        <div class="cart-item-price">
                                            Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                        </div>

                                        @if ($itemStock > 10)
                                            <span class="cart-item-stock-tag stock-available">Tersedia ({{ $itemStock }} unit)</span>
                                        @elseif ($itemStock >= 1)
                                            <span class="cart-item-stock-tag stock-limited">Stok Terbatas (Tersisa {{ $itemStock }} unit)</span>
                                        @else
                                            <span class="cart-item-stock-tag stock-out">Stok Habis</span>
                                        @endif
                                    </div>

                                    <!-- AKSI & QTY -->
                                    <div class="cart-item-actions">
                                        <div class="qty-control">
                                            <button
                                                type="button"
                                                class="qty-btn"
                                                onclick="changeCartQty({{ $item->cart_id }}, -1, {{ $itemStock }})"
                                                {{ $item->qty <= 1 ? 'disabled' : '' }}
                                            >-</button>
                                            <input
                                                type="text"
                                                class="qty-input"
                                                id="qtyInput-{{ $item->cart_id }}"
                                                value="{{ $item->qty }}"
                                                readonly
                                            >
                                            <button
                                                type="button"
                                                class="qty-btn"
                                                onclick="changeCartQty({{ $item->cart_id }}, 1, {{ $itemStock }})"
                                                {{ $item->qty >= $itemStock ? 'disabled' : '' }}
                                            >+</button>
                                        </div>

                                        <div class="cart-item-subtotal-val" id="subtotalVal-{{ $item->cart_id }}" style="font-size: 14px; font-weight: 750; color: #10245c;">
                                            Rp {{ number_format($itemSubtotal, 0, ',', '.') }}
                                        </div>

                                        <button
                                            type="button"
                                            class="delete-item-btn"
                                            onclick="deleteCartItem({{ $item->cart_id }})"
                                            style="display: inline-flex; align-items: center; gap: 4px;"
                                        >
                                            <i data-lucide="trash-2" style="width: 13px; height: 13px;"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- RINGKASAN PEMBELIAN -->
                        <div class="cart-summary-card">
                            <h3 class="summary-title">Ringkasan Belanja</h3>

                            <div class="summary-row">
                                <span>Total Item Dipilih</span>
                                <strong id="selectedItemCount">0 Barang</strong>
                            </div>

                            <div class="summary-row">
                                <span>Biaya Pengiriman</span>
                                <strong style="color: #15803d;">Gratis (Promo)</strong>
                            </div>

                            <div class="summary-row total-row">
                                <span>Total Belanja</span>
                                <span class="total-price" id="selectedTotalPrice">Rp 0</span>
                            </div>

                            <button type="submit" class="checkout-submit-btn" id="btnCheckout">
                                Lanjut ke Checkout
                            </button>

                            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 14px;">
                                Jaminan transaksi aman dengan proteksi garansi resmi NexTech.
                            </p>
                        </div>
                    </div>
                </form>

                <!-- Hidden Delete Form -->
                <form id="deleteCartForm" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

        <!-- ============================================== -->
        <!-- TAB 2: RIWAYAT PESANAN                         -->
        <!-- ============================================== -->
        @else
            @if ($orders->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i data-lucide="package" style="width: 48px; height: 48px; color: #94a3b8;"></i>
                    </div>
                    <h3>Belum Ada Riwayat Pesanan</h3>
                    <p>Anda belum pernah melakukan pemesanan barang. Pesanan yang berhasil diproses akan tampil di sini.</p>
                    <a href="{{ route('products.index') }}" class="buy-button" style="display: inline-block; width: auto; padding: 12px 28px;">
                        Mulai Belanja
                    </a>
                </div>
            @else
                @php
                    $groupedOrders = $orders->groupBy('trx_id');
                @endphp
                <div class="orders-list">
                    @foreach ($groupedOrders as $trxId => $items)
                        @php
                            $firstItem = $items->first();
                            $trxTotal = $items->sum(function ($it) {
                                return (int) ($it->total_amt ?: ($it->qty * $it->product_price));
                            });

                            $paymentStatusStr = strtolower((string) ($firstItem->p_status ?? 'pending'));
                            $orderStatusStr = strtolower((string) ($firstItem->order_status ?? 'menunggu_pembayaran'));
                            $rawStatus = $orderStatusStr;
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
                                $stepIndex = 2; // Tahap Terbayar
                            } else {
                                $stepIndex = 1; // Menunggu Pembayaran
                            }
                        @endphp
                        <div class="order-card">
                            <div class="order-card-header">
                                <div class="order-meta">
                                    <span>No. Pesanan: <strong>#{{ $trxId }}</strong></span>
                                    <span>&bull;</span>
                                    <span>Tanggal: {{ date('d M Y, H:i', strtotime($firstItem->created_at ?? now())) }}</span>
                                    <span>&bull;</span>
                                    <span>Metode: <strong>{{ $firstItem->payment_method ?? 'Midtrans' }}</strong></span>
                                </div>

                                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                    {{-- Status Pembayaran Tunggal & Konsisten --}}
                                    @if($isPaid)
                                        <span class="order-status-badge status-completed">Terbayar</span>
                                    @elseif($isCancelled)
                                        <span class="order-status-badge status-cancelled">Dibatalkan</span>
                                    @else
                                        <span class="order-status-badge status-pending">Menunggu Pembayaran</span>
                                    @endif

                                    {{-- Status Pemrosesan Pesanan (Ditampilkan setelah pembayaran berhasil) --}}
                                    @if($isPaid && !$isCancelled)
                                        @if($orderStatusStr === 'sedang_diproses')
                                            <span class="order-status-badge status-process">Sedang Diproses</span>
                                        @elseif($orderStatusStr === 'dikirim')
                                            <span class="order-status-badge status-shipping">Sedang Dikirim</span>
                                        @elseif($orderStatusStr === 'selesai')
                                            <span class="order-status-badge status-completed">Pesanan Selesai</span>
                                        @else
                                            <span class="order-status-badge status-info">Menunggu Diproses</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            @if($stepIndex > 0)
                                <!-- MINI TRACKER STEPPER -->
                                <div class="order-mini-tracker">
                                    <div class="tracker-step {{ $stepIndex > 1 ? 'done' : ($stepIndex === 1 ? 'active' : '') }}">
                                        <div class="tracker-dot"></div>
                                        <span class="tracker-label">Dibuat</span>
                                    </div>
                                    <div class="tracker-step {{ $stepIndex > 2 ? 'done' : ($stepIndex === 2 ? 'active' : '') }}">
                                        <div class="tracker-dot"></div>
                                        <span class="tracker-label">Terbayar</span>
                                    </div>
                                    <div class="tracker-step {{ $stepIndex > 3 ? 'done' : ($stepIndex === 3 ? 'active' : '') }}">
                                        <div class="tracker-dot"></div>
                                        <span class="tracker-label">Diproses</span>
                                    </div>
                                    <div class="tracker-step {{ $stepIndex > 4 ? 'done' : ($stepIndex === 4 ? 'active' : '') }}">
                                        <div class="tracker-dot"></div>
                                        <span class="tracker-label">Dikirim</span>
                                    </div>
                                    <div class="tracker-step {{ $stepIndex === 5 ? 'done' : '' }}">
                                        <div class="tracker-dot"></div>
                                        <span class="tracker-label">Selesai</span>
                                    </div>
                                </div>
                            @endif

                            @if(($orderStatusStr === 'dikirim' || $rawStatus === 'dikirim') && (!empty($firstItem->courier_name) || !empty($firstItem->tracking_number)))
                                <div class="shipping-info-banner">
                                    <div>
                                        <strong>Pengiriman Sedang Berjalan:</strong>
                                        Kurir <strong>{{ $firstItem->courier_name ?? 'Ekspedisi' }}</strong> &bull; Resi: <strong>{{ $firstItem->tracking_number ?? '-' }}</strong>
                                        @if(!empty($firstItem->shipping_notes))
                                            <div style="font-size: 12px; color: #475569; margin-top: 2px;">Catatan: {{ $firstItem->shipping_notes }}</div>
                                        @endif
                                    </div>
                                    <form action="{{ route('user.orders.confirm-received', $trxId) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin telah menerima paket pesanan ini dengan kondisi baik?');" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-confirm-received">
                                            Pesanan Diterima
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- DAFTAR ITEM PESANAN -->
                            @foreach ($items as $item)
                                <div class="order-card-body" style="border-bottom: 1px solid #f1f5f9; padding: 12px 0;">
                                    <div class="order-thumb">
                                        @if (!empty($item->product_image))
                                            <img src="{{ asset('assets/images/' . $item->product_image) }}" alt="{{ $item->product_title }}">
                                        @else
                                            <span>No Image</span>
                                        @endif
                                    </div>

                                    <div class="order-product-info">
                                        <h4>
                                            <a href="{{ route('products.show', $item->product_id) }}" style="color: inherit; text-decoration: none;">
                                                {{ $item->product_title }}
                                            </a>
                                        </h4>
                                        <p>{{ $item->qty }} Unit &times; Rp {{ number_format($item->product_price, 0, ',', '.') }}</p>
                                    </div>

                                    <div style="text-align: right;">
                                        <div style="font-size: 12px; color: #64748b;">Subtotal</div>
                                        <strong style="color: #10245c; font-size: 14px;">
                                            Rp {{ number_format($item->total_amt ?: ($item->qty * $item->product_price), 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>
                            @endforeach

                            <!-- FOOTER PESANAN: TOTAL & AKSI -->
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; flex-wrap: wrap; gap: 12px;">
                                <div>
                                    <span style="font-size: 13px; color: #64748b;">Total Pembayaran ({{ $items->sum('qty') }} Barang):</span>
                                    <div style="font-size: 18px; font-weight: 800; color: #087cff;">
                                        Rp {{ number_format($trxTotal, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                    <a href="{{ route('user.orders.detail', $trxId) }}" class="btn-detail-order">
                                        Lacak & Detail Pesanan
                                    </a>

                                    @if (!$isPaid && !$isCancelled && $orderStatusStr === 'menunggu_pembayaran' && strtolower($firstItem->payment_method ?? '') === 'midtrans')
                                        <a href="{{ route('user.payment', $trxId) }}" style="display: inline-block; padding: 8px 18px; background: #087cff; color: #ffffff; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
                                            Bayar Sekarang
                                        </a>
                                    @endif

                                    @if ($orderStatusStr === 'selesai')
                                        <a href="{{ route('products.index') }}" class="btn-detail-order" style="color: #10b981; border: 1px solid #a7f3d0; background: #ecfdf5;">
                                            Beli Lagi
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>

    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')

    <script>
        // Update Cart Total Price and Selected Count
        function updateCartTotals() {
            const checkboxes = document.querySelectorAll('.item-check:checked');
            let totalQty = 0;
            let totalPrice = 0;

            checkboxes.forEach(cb => {
                const qty = parseInt(cb.getAttribute('data-qty'), 10) || 0;
                const price = parseInt(cb.getAttribute('data-price'), 10) || 0;
                totalQty += qty;
                totalPrice += (qty * price);
            });

            const countEl = document.getElementById('selectedItemCount');
            const totalEl = document.getElementById('selectedTotalPrice');
            const btnCheckout = document.getElementById('btnCheckout');

            if (countEl) countEl.textContent = totalQty + ' Barang';
            if (totalEl) totalEl.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');

            if (btnCheckout) {
                btnCheckout.disabled = (checkboxes.length === 0);
            }

            // Sync master select all checkbox
            const allCheckboxes = document.querySelectorAll('.item-check:not(:disabled)');
            const selectAllCb = document.getElementById('selectAllCheckbox');
            if (selectAllCb) {
                selectAllCb.checked = (allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length);
            }
        }

        function toggleSelectAll(masterCb) {
            const checkboxes = document.querySelectorAll('.item-check:not(:disabled)');
            checkboxes.forEach(cb => {
                cb.checked = masterCb.checked;
            });
            updateCartTotals();
        }

        async function changeCartQty(cartId, delta, maxStock) {
            const input = document.getElementById('qtyInput-' + cartId);
            if (!input) return;

            let currentQty = parseInt(input.value, 10) || 1;
            let targetQty = currentQty + delta;

            if (targetQty < 1) targetQty = 1;
            if (targetQty > maxStock) {
                alert('Maksimal pembelian untuk produk ini adalah ' + maxStock + ' unit sesuai stok database.');
                return;
            }

            try {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const response = await fetch('{{ route("user.cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        cart_id: cartId,
                        qty: targetQty
                    })
                });

                const data = await response.json();
                if (data.success) {
                    input.value = data.qty;
                    const subtotalEl = document.getElementById('subtotalVal-' + cartId);
                    if (subtotalEl) {
                        subtotalEl.textContent = data.subtotal_formatted;
                    }

                    // Update data-qty attribute on checkbox
                    const cb = document.querySelector('.item-check[data-cart-id="' + cartId + '"]');
                    if (cb) {
                        cb.setAttribute('data-qty', data.qty);
                    }

                    // Update badge on navbar
                    const navBadge = document.getElementById('navCartBadge');
                    if (navBadge) {
                        navBadge.textContent = data.count;
                        navBadge.style.display = data.count > 0 ? '' : 'none';
                    }

                    updateCartTotals();
                } else {
                    alert(data.message || 'Gagal mengubah jumlah.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            }
        }

        function deleteCartItem(cartId) {
            if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang belanja?')) {
                return;
            }
            const form = document.getElementById('deleteCartForm');
            form.action = '{{ url("/user/cart") }}/' + cartId;
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCartTotals();
        });
    </script>
</body>
</html>
