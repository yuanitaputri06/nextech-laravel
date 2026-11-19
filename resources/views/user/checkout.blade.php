<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTech - Checkout & Pembayaran</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .page-container {
            width: 90%;
            max-width: 1100px;
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

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 28px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .checkout-grid {
                grid-template-columns: 1fr;
            }
        }

        .checkout-section-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.05), 0 1px 3px rgba(16, 36, 92, 0.03);
        }

        .checkout-section-title {
            font-size: 17px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-badge {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #087cff;
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #087cff;
            box-shadow: 0 0 0 3px rgba(8, 124, 255, 0.15);
        }

        /* PAYMENT METHOD CARDS */
        .payment-options {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .payment-option-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-option-card:hover {
            border-color: #93c5fd;
            background: #f8fafc;
        }

        .payment-option-card.selected {
            border-color: #087cff;
            background: #eff6ff;
        }

        .payment-radio {
            margin-top: 3px;
            width: 18px;
            height: 18px;
            accent-color: #087cff;
            cursor: pointer;
        }

        .payment-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 4px;
        }

        .payment-info p {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        .bank-details-box {
            margin-top: 10px;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px dashed #bfdbfe;
            border-radius: 8px;
            font-size: 13px;
            color: #1e3a8a;
        }

        /* ORDER SUMMARY SIDEBAR */
        .order-summary-sidebar {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            padding: 22px;
            position: sticky;
            top: 96px;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.07), 0 2px 6px rgba(16, 36, 92, 0.03);
        }

        .summary-sidebar-title {
            font-size: 17px;
            font-weight: 750;
            color: #10245c;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 16px;
        }

        .summary-items-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            max-height: 280px;
            overflow-y: auto;
            margin-bottom: 18px;
            padding-right: 4px;
        }

        .summary-item-row {
            display: grid;
            grid-template-columns: 50px 1fr auto;
            gap: 12px;
            align-items: center;
        }

        .summary-item-thumb {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .summary-item-thumb img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .summary-item-info h5 {
            font-size: 13px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .summary-item-info span {
            font-size: 12px;
            color: #64748b;
        }

        .summary-item-price {
            font-size: 13px;
            font-weight: 700;
            color: #087cff;
            text-align: right;
        }

        .calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .calc-row.total-calc {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px dashed #cbd5e1;
            font-size: 16px;
            font-weight: 750;
            color: #10245c;
        }

        .calc-row.total-calc .total-val {
            font-size: 20px;
            color: #087cff;
        }

        .btn-pay-now {
            width: 100%;
            padding: 14px;
            background: #087cff;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 750;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.3);
            transition: all 0.2s ease;
            margin-top: 18px;
        }

        .btn-pay-now:hover {
            background: #066bdc;
            box-shadow: 0 6px 18px rgba(8, 124, 255, 0.4);
            transform: translateY(-1px);
        }

        .alert-box {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
    </style>
</head>
<body>

    @include('partials.navbar')

    <div class="page-container">
        <div class="page-header">
            <h1>Checkout & Pembayaran</h1>
            <p>Periksa rincian pesanan dan selesaikan pembayaran untuk memproses pesanan Anda.</p>
        </div>

        @if(session('error'))
            <div class="alert-box alert-error" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="circle-x" style="width: 16px; height: 16px;"></i>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
            </div>
        @endif

        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf

            <div class="checkout-grid">
                <!-- SISI KIRI: DATA PENGIRIMAN & METODE PEMBAYARAN -->
                <div class="checkout-main">
                    <!-- SECTION 1: DATA PENERIMA & ALAMAT -->
                    <div class="checkout-section-card">
                        <div class="checkout-section-title">
                            <span class="step-badge">1</span>
                            <span>Informasi Pengiriman & Pembeli</span>
                        </div>

                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label for="recipient_name" class="form-label">Nama Lengkap Penerima *</label>
                                <input
                                    type="text"
                                    id="recipient_name"
                                    name="recipient_name"
                                    class="form-input"
                                    value="{{ old('recipient_name', trim($user->first_name . ' ' . $user->last_name)) }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Nomor Telepon / WhatsApp *</label>
                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    class="form-input"
                                    value="{{ old('phone', $user->mobile) }}"
                                    placeholder="Contoh: 081234567890"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Alamat Email *</label>
                                <input
                                    type="email"
                                    id="email"
                                    class="form-input"
                                    value="{{ $user->email }}"
                                    readonly
                                    style="background: #f8fafc; color: #64748b;"
                                >
                            </div>

                            <div class="form-group full-width">
                                <label for="address" class="form-label">Alamat Lengkap Pengiriman *</label>
                                <textarea
                                    id="address"
                                    name="address"
                                    class="form-input"
                                    rows="3"
                                    style="resize: vertical;"
                                    placeholder="Tuliskan nama jalan, no. rumah, RT/RW, kecamatan, kota, dan kode pos"
                                    required
                                >{{ old('address', ($user->address1 ? ($user->address1 . ($user->address2 ? ', ' . $user->address2 : '')) : '')) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: METODE PEMBAYARAN -->
                    <div class="checkout-section-card">
                        <div class="checkout-section-title">
                            <span class="step-badge">2</span>
                            <span>Pilih Metode Pembayaran</span>
                        </div>

                        <div class="payment-options">
                            <!-- MIDTRANS PAYMENT GATEWAY -->
                            <label class="payment-option-card selected" id="optMidtrans">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="Midtrans"
                                    class="payment-radio"
                                    checked
                                    id="radioMidtrans"
                                >
                                <div class="payment-info">
                                    <h4>Midtrans Snap (Pembayaran Online Otomatis)</h4>
                                    <p>Bayar instan melalui QRIS (GoPay, OVO, Dana), Virtual Account (BCA, Mandiri, BRI, BNI, Permata), ShopeePay, atau Kartu Kredit.</p>
                                    <div style="font-size: 12px; color: #087cff; margin-top: 6px; font-weight: 600;">
                                        Verifikasi otomatis 24 jam tanpa perlu unggah bukti transfer
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SISI KANAN: RINGKASAN PESANAN -->
                <div class="checkout-sidebar">
                    <div class="order-summary-sidebar">
                        <h3 class="summary-sidebar-title">Ringkasan Pesanan</h3>

                        <div class="summary-items-list">
                            @foreach ($checkoutItems as $index => $item)
                                <!-- HIDDEN INPUT UNTUK CONTROLLER -->
                                <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                <input type="hidden" name="items[{{ $index }}][qty]" value="{{ $item->qty }}">
                                @if(!empty($item->cart_id))
                                    <input type="hidden" name="items[{{ $index }}][cart_id]" value="{{ $item->cart_id }}">
                                @endif

                                <div class="summary-item-row">
                                    <div class="summary-item-thumb">
                                        @if(!empty($item->product_image))
                                            <img src="{{ asset('assets/images/' . $item->product_image) }}" alt="{{ $item->product_title }}">
                                        @else
                                            <span>No Img</span>
                                        @endif
                                    </div>

                                    <div class="summary-item-info">
                                        <h5>{{ $item->product_title }}</h5>
                                        <span>{{ $item->qty }} &times; Rp {{ number_format($item->product_price, 0, ',', '.') }}</span>
                                    </div>

                                    <div class="summary-item-price">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="calc-row">
                            <span>Total Harga Barang</span>
                            <strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong>
                        </div>

                        <div class="calc-row">
                            <span>Ongkos Kirim</span>
                            <strong style="color: #15803d;">Gratis (Rp 0)</strong>
                        </div>

                        <div class="calc-row total-calc">
                            <span>Total Pembayaran</span>
                            <span class="total-val">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="btn-pay-now">
                            Konfirmasi & Bayar Sekarang
                        </button>

                        <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 14px; line-height: 1.5; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <i data-lucide="lock" style="width: 14px; height: 14px;"></i>
                            <span>Transaksi dilindungi enkripsi aman dan garansi resmi toko NexTech.</span>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')

</body>
</html>
