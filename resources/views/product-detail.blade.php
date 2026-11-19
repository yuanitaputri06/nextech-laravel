
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->product_title }} | NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #1769e0;
            --primary-dark: #0d4eaf;
            --text: #172033;
            --muted: #788397;
            --border: #e8edf4;
            --soft: #f4f7fb;
            --white: #ffffff;
        }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #edf7ff 0%, #e5f2ff 38%, #f3f8ff 70%, #eaf4ff 100%);
            background-attachment: fixed;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font: inherit;
            cursor: pointer;
        }

        .page-shell {
            max-width: 1440px;
            width: calc(100% - 48px);
            margin: 36px auto;
            padding: 0 42px 50px;
            background: var(--white);
            border: 1px solid #dde7f3;
            border-radius: 14px;
            min-height: 90vh;
            box-shadow: 0 8px 32px rgba(8, 124, 255, 0.06), 0 2px 8px rgba(16, 36, 92, 0.04);
            position: relative;
            z-index: 1;
        }

        /* NAVBAR */

        .navbar {
            min-height: 88px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--primary);
            white-space: nowrap;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            font-size: 24px;
            font-weight: 800;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            font-size: 14px;
            color: #303a4b;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f4f6f9;
            border-radius: 30px;
            padding: 11px 16px;
            width: 245px;
        }

        .search-box input {
            border: none;
            outline: none;
            background: transparent;
            width: 100%;
            min-width: 0;
            font-size: 13px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 13px;
            white-space: nowrap;
        }

        .nav-actions a:hover {
            color: var(--primary);
        }

        /* BREADCRUMB */

        .breadcrumb {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin: 22px 0 28px;
            color: var(--muted);
            font-size: 13px;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .breadcrumb .current {
            color: var(--text);
            font-weight: 600;
        }

        /* PRODUCT DETAIL */

        .product-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(0, .92fr);
            gap: 52px;
            align-items: start;
        }

        .gallery-main {
            background: #f4f7fa;
            border: 1px solid #f0f3f7;
            border-radius: 14px;
            height: 490px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px;
            overflow: hidden;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform .35s ease;
        }

        .gallery-main:hover img {
            transform: scale(1.035);
        }

        .image-placeholder {
            color: #9aa6b7;
            font-size: 15px;
            text-align: center;
        }

        .thumbnail-list {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .thumbnail {
            height: 105px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #f5f7fa;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .thumbnail.active {
            border: 2px solid var(--primary);
            background: #eff6ff;
        }

        .thumbnail:hover {
            border-color: var(--primary);
        }

        /* PRODUCT INFORMATION */

        .product-info {
            padding-top: 3px;
        }

        .category-label {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            background: #edf4ff;
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            margin-bottom: 12px;
        }

        .product-title {
            font-size: clamp(27px, 3vw, 37px);
            line-height: 1.2;
            letter-spacing: -.8px;
            font-weight: 750;
            overflow-wrap: anywhere;
        }

        .product-description-short {
            color: var(--muted);
            font-size: 14px;
            margin-top: 13px;
            line-height: 1.8;
            text-align: justify;
        }

        .rating-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
            font-size: 13px;
        }

        .stars {
            color: #f5a623;
            letter-spacing: 2px;
            font-size: 17px;
        }

        .rating-note {
            color: var(--muted);
        }

        .section-line {
            border: none;
            border-top: 1px solid var(--border);
            margin: 24px 0;
        }

        .price {
            color: var(--primary);
            font-size: clamp(25px, 2.8vw, 33px);
            font-weight: 800;
            letter-spacing: -.5px;
            line-height: 1.35;
        }

        .price-note {
            color: var(--muted);
            font-size: 12px;
            margin-top: 6px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 13px;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #13834c;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .stock-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #20b56b;
        }

        .quantity-row {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 148px;
            background: #f3f6fa;
            border-radius: 28px;
            padding: 5px 9px;
        }

        .quantity-control button {
            border: none;
            background: transparent;
            width: 32px;
            height: 32px;
            font-size: 21px;
            color: #435169;
        }

        .quantity-control input {
            width: 38px;
            text-align: center;
            border: none;
            outline: none;
            background: transparent;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }

        .quantity-note {
            color: var(--muted);
            font-size: 12px;
        }

        .button-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 24px;
        }

        .btn {
            min-height: 48px;
            border-radius: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 700;
            transition: .2s ease;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-outline:hover {
            background: #eff6ff;
        }

        .btn:disabled,
        .btn[disabled] {
            background: #e2e8f0 !important;
            border-color: #cbd5e1 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            box-shadow: none !important;
            pointer-events: auto !important;
        }

        .quantity-control.disabled {
            opacity: 0.55;
            background: #e2e8f0;
            cursor: not-allowed;
        }

        .quantity-control.disabled button,
        .quantity-control.disabled input {
            cursor: not-allowed;
            color: #94a3b8;
        }

        /* DELIVERY INFORMATION */

        .service-card {
            margin-top: 26px;
            border: 1px solid var(--border);
            border-radius: 11px;
            overflow: hidden;
        }

        .service-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 17px 18px;
        }

        .service-item + .service-item {
            border-top: 1px solid var(--border);
        }

        .service-icon {
            color: var(--primary);
            font-size: 21px;
            line-height: 1.2;
        }

        .service-title {
            font-size: 13px;
            font-weight: 700;
        }

        .service-text {
            color: var(--muted);
            font-size: 12px;
            margin-top: 4px;
        }

        /* DESCRIPTION */

        .details-section {
            margin-top: 48px;
            padding-top: 28px;
            border-top: 1px solid var(--border);
        }

        .details-heading {
            font-size: 22px;
            margin-bottom: 15px;
        }

        .details-box {
            padding: 23px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: #596579;
            font-size: 14px;
            line-height: 1.9;
            white-space: pre-line;
            word-break: break-word;
            text-align: justify;
            position: relative;
            transition: background 0.2s ease, border-color 0.2s ease;
        }

        .details-box.is-clamped {
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }

        .details-box.is-clamped:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .details-box.expanded {
            display: block;
            overflow: visible;
            -webkit-line-clamp: unset;
            cursor: default;
        }

        .desc-toggle-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 14px;
            background: #f0f7ff;
            color: #087cff;
            border: 1px solid #bfdbfe;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .desc-toggle-btn:hover {
            background: #087cff;
            color: #ffffff;
            border-color: #087cff;
        }

        .desc-toggle-btn .toggle-arrow {
            display: inline-block;
            transition: transform 0.25s ease;
            font-style: normal;
            font-size: 11px;
        }

        .desc-toggle-btn.expanded .toggle-arrow {
            transform: rotate(180deg);
        }

        /* WISHLIST BUTTON */
        .btn-wishlist {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 20px;
            border-radius: 10px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #475569;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .btn-wishlist:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: #fef2f2;
        }

        .btn-wishlist.active {
            border-color: #ef4444;
            background: #fef2f2;
            color: #dc2626;
        }

        .btn-wishlist.active .heart-icon {
            transform: scale(1.15);
        }

        .heart-icon {
            font-size: 18px;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        /* REVIEWS SECTION */
        .reviews-section {
            margin-top: 48px;
            padding-top: 32px;
            border-top: 1px solid var(--border);
        }

        .rating-summary-banner {
            display: flex;
            align-items: center;
            gap: 18px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 24px;
            margin-bottom: 24px;
        }

        .big-rating-num {
            font-size: 38px;
            font-weight: 800;
            color: #10245c;
            line-height: 1;
        }

        .review-form-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }

        .star-rating-selector {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .star-select-item {
            font-size: 26px;
            color: #cbd5e1;
            cursor: pointer;
            transition: color 0.15s, transform 0.15s;
            user-select: none;
        }

        .star-select-item.active {
            color: #f59e0b;
        }

        .star-select-item:hover {
            transform: scale(1.15);
        }

        .review-textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }

        .review-textarea:focus {
            border-color: #087cff;
        }

        .reviews-list-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
        }

        .single-review-item {
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
        }

        .rev-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #087cff, #0056b3);
            color: #ffffff;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            overflow: hidden;
        }

        .rev-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* RECOMMENDATIONS */

        .recommendation-section {
            margin-top: 42px;
        }

        .section-heading-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .section-heading-row h2 {
            font-size: 22px;
        }

        .section-heading-row a {
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
        }

        .recommendation-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .recommendation-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.04);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .recommendation-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(8, 124, 255, 0.13), 0 4px 10px rgba(16, 36, 92, 0.05);
            border-color: #b9d7fb;
        }

        .recommendation-image {
            height: 190px;
            background: #f4f7fa;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .recommendation-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .recommendation-content {
            padding: 16px;
        }

        .recommendation-category {
            color: var(--muted);
            font-size: 11px;
            margin-bottom: 5px;
        }

        .recommendation-name {
            font-size: 14px;
            font-weight: 700;
            min-height: 44px;
        }

        .recommendation-price {
            color: var(--primary);
            font-size: 17px;
            font-weight: 800;
            margin-top: 8px;
        }

        .empty-recommendations {
            color: var(--muted);
            font-size: 14px;
        }

        /* FOOTER */

        .footer {
            border-top: 1px solid var(--border);
            margin-top: 45px;
            padding: 22px 0 0;
            text-align: center;
            color: var(--muted);
            font-size: 12px;
        }

        /* RESPONSIVE */

        @media (max-width: 1050px) {
            .page-shell {
                width: calc(100% - 28px);
                padding: 0 25px 35px;
                margin: 18px auto;
            }

            .navbar {
                flex-wrap: wrap;
                padding: 20px 0;
            }

            .nav-links {
                gap: 15px;
            }

            .search-box {
                width: 200px;
            }

            .product-layout {
                gap: 30px;
            }

            .gallery-main {
                height: 390px;
            }

            .thumbnail {
                height: 80px;
            }
        }

        @media (max-width: 760px) {
            .page-shell {
                width: 100%;
                margin: 0;
                padding: 0 17px 28px;
                border-radius: 0;
            }

            .navbar {
                gap: 16px;
            }

            .brand {
                font-size: 24px;
            }

            .nav-links {
                order: 3;
                width: 100%;
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .search-box {
                order: 2;
                width: 100%;
            }

            .nav-actions {
                margin-left: auto;
                gap: 12px;
            }

            .breadcrumb {
                font-size: 11px;
                margin: 18px 0;
            }

            .product-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .gallery-main {
                height: min(82vw, 420px);
                padding: 25px;
            }

            .thumbnail-list {
                gap: 8px;
                margin-top: 10px;
            }

            .thumbnail {
                height: 76px;
                padding: 7px;
            }

            .product-title {
                font-size: 29px;
            }

            .section-line {
                margin: 19px 0;
            }

            .button-row {
                gap: 10px;
            }

            .btn {
                font-size: 12px;
                padding: 10px 8px;
            }

            .service-card {
                margin-top: 20px;
            }

            .details-section {
                margin-top: 32px;
            }

            .recommendation-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .recommendation-image {
                height: 145px;
                padding: 12px;
            }

            .recommendation-content {
                padding: 12px;
            }

            .recommendation-name {
                font-size: 12px;
            }

            .recommendation-price {
                font-size: 15px;
            }
        }

        @media (max-width: 380px) {
            .nav-actions {
                font-size: 11px;
            }

            .button-row {
                grid-template-columns: 1fr;
            }

            .quantity-row {
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    @include('partials.navbar')

@php
    $imagePath = !empty($product->product_image)
        ? asset('assets/images/' . $product->product_image)
        : null;

    $categoryName = $product->cat_title ?? 'Produk NexTech';
    $description = $product->product_desc ?? 'Belum ada deskripsi produk.';
@endphp

<div class="page-shell">

    <!-- BREADCRUMB -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Beranda</a>
        <span>/</span>
        <a href="{{ route('products.index') }}">Produk</a>
        <span>/</span>
        <span>{{ $categoryName }}</span>
        <span>/</span>
        <span class="current">{{ $product->product_title }}</span>
    </nav>

    <!-- DETAIL PRODUK -->
    <main>
        <section class="product-layout">

            <!-- KIRI: GALERI -->
            <div class="product-gallery">
                <div class="gallery-main">
                    @if ($imagePath)
                        <img
                            id="mainProductImage"
                            src="{{ $imagePath }}"
                            alt="{{ $product->product_title }}"
                            onerror="this.style.display='none'; document.getElementById('imageFallback').style.display='block';"
                        >
                        <div id="imageFallback" class="image-placeholder"
                             style="display:none;">
                            Gambar produk tidak ditemukan.
                        </div>
                    @else
                        <div class="image-placeholder">
                            Gambar produk belum tersedia.
                        </div>
                    @endif
                </div>

                <div class="thumbnail-list">
                    @for ($i = 0; $i < 4; $i++)
                        <button
                            type="button"
                            class="thumbnail {{ $i === 0 ? 'active' : '' }}"
                            aria-label="Lihat gambar produk"
                            @if ($imagePath)
                                onclick="changeProductImage(this)"
                                data-image="{{ $imagePath }}"
                            @endif
                        >
                            @if ($imagePath)
                                <img src="{{ $imagePath }}"
                                     alt="{{ $product->product_title }}">
                            @else
                                <span style="font-size:12px;color:#8994a5">
                                    No image
                                </span>
                            @endif
                        </button>
                    @endfor
                </div>
            </div>

            <!-- KANAN: INFORMASI PRODUK -->
            <div class="product-info">
                <span class="category-label">
                    {{ $categoryName }}
                </span>

                <h1 class="product-title">
                    {{ $product->product_title }}
                </h1>

                <div class="product-description-short" id="topDescWrapper">
                    <span id="topDescText">{{ \Illuminate\Support\Str::limit(strip_tags($description), 180) }}</span>
                    @if(strlen(strip_tags($description)) > 180)
                        <button type="button" id="topDescBtn" onclick="toggleTopDesc()" style="background: none; border: none; color: #087cff; font-weight: 600; cursor: pointer; padding: 0; font-size: 13px; text-decoration: underline; margin-left: 4px;">
                            Baca selengkapnya
                        </button>
                    @endif
                </div>

                <div class="rating-row" style="display: flex; align-items: center; gap: 8px;">
                    <span class="stars" style="color: #f59e0b; font-size: 16px;">
                        @for ($i = 1; $i <= 5; $i++)
                            {{ $i <= round($averageRating ?? 0) ? '★' : '☆' }}
                        @endfor
                    </span>
                    <span class="rating-score" style="font-weight: 700; color: #10245c;">
                        {{ ($averageRating ?? 0) > 0 ? number_format($averageRating, 1) : '0.0' }}
                    </span>
                    <a href="#ulasan-produk" class="rating-note" style="color: #64748b; text-decoration: underline;">
                        ({{ $reviewCount ?? 0 }} Ulasan)
                    </a>
                </div>

                <hr class="section-line">

                <div class="price">
                    Rp{{ number_format((float) $product->product_price, 0, ',', '.') }}
                </div>

                <p class="price-note">
                    Harga produk sesuai database.
                </p>

                <hr class="section-line">

                <h2 class="section-title">Informasi Produk</h2>

                @php
                    $prodStock = (int) ($product->stock ?? 0);
                    $isAvailable = $prodStock > 0;
                @endphp

                <div class="stock-display-wrapper" style="margin-bottom: 18px;">
                    @if ($isAvailable)
                        <div class="stock-status" style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #13834c;">
                            <span class="stock-dot" style="width: 9px; height: 9px; border-radius: 50%; background: #20b56b; display: inline-block;"></span>
                            <span id="stockStatusText">Stok Tersedia: {{ $prodStock }} Unit</span>
                        </div>
                    @else
                        <div class="stock-status out-of-stock" style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; color: #dc2626;">
                            <span class="stock-dot" style="width: 9px; height: 9px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                            <span id="stockStatusText">Stok Habis</span>
                        </div>
                    @endif
                </div>

                <div class="quantity-row">
                    <div class="quantity-control {{ !$isAvailable ? 'disabled' : '' }}" id="qtyControlBox">
                        <button type="button" onclick="changeQuantity(-1)"
                                aria-label="Kurangi jumlah" {{ !$isAvailable ? 'disabled' : '' }}>−</button>
                        <input id="quantity" type="number" value="{{ $isAvailable ? 1 : 0 }}"
                               min="{{ $isAvailable ? 1 : 0 }}" max="{{ $prodStock }}" readonly
                               data-max-stock="{{ $prodStock }}"
                               aria-label="Jumlah produk">
                        <button type="button" onclick="changeQuantity(1)"
                                aria-label="Tambah jumlah" {{ !$isAvailable ? 'disabled' : '' }}>+</button>
                    </div>

                    <span class="quantity-note" id="quantityNote">
                        @if ($isAvailable)
                            Pilih jumlah produk (maksimal {{ $prodStock }} unit)
                        @else
                            <span style="color: #dc2626; font-weight: 600;">Stok habis, pemesanan dinonaktifkan</span>
                        @endif
                    </span>
                </div>

                @if(session('is_admin'))
                    <div style="background: rgba(8, 124, 255, 0.08); border: 1px solid rgba(8, 124, 255, 0.25); border-radius: 10px; padding: 14px 18px; color: #1e3a8a; display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                        <i data-lucide="shield-check" style="width: 20px; height: 20px; color: #087cf5; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; font-size: 13px; color: #0f172a;">Mode Administrator Aktif</strong>
                            <span style="font-size: 12px; color: #475569;">Fitur transaksi (Beli, Keranjang, Wishlist) dinonaktifkan pada pratinjau katalog produk untuk Admin.</span>
                        </div>
                    </div>
                @else
                    <div class="button-row" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary" id="btnBuyNow"
                                onclick="buyProduct()" {{ !$isAvailable ? 'disabled' : '' }}>
                            Beli Sekarang
                        </button>

                        <button type="button" class="btn btn-outline" id="btnAddToCart"
                                onclick="addToCart()" {{ !$isAvailable ? 'disabled' : '' }}>
                            <i data-lucide="plus" style="width: 15px; height: 15px; vertical-align: -2px; margin-right: 4px;"></i> Tambah ke Keranjang
                        </button>

                        <button type="button" class="btn-wishlist {{ ($isInWishlist ?? false) ? 'active' : '' }}" id="btnWishlist"
                                onclick="toggleWishlist({{ $product->product_id }})"
                                title="{{ ($isInWishlist ?? false) ? 'Hapus dari Wishlist' : 'Simpan ke Wishlist' }}">
                            <span class="heart-icon">
                                <i data-lucide="heart" style="width: 16px; height: 16px; vertical-align: -2px; fill: {{ ($isInWishlist ?? false) ? '#ef4444' : 'none' }}; stroke: {{ ($isInWishlist ?? false) ? '#ef4444' : 'currentColor' }};"></i>
                            </span>
                            <span class="wishlist-text">{{ ($isInWishlist ?? false) ? 'Tersimpan' : 'Wishlist' }}</span>
                        </button>
                    </div>
                @endif

                <div class="service-card">
                    <div class="service-item">
                        <span class="service-icon"><i data-lucide="truck" style="width: 18px; height: 18px; color: #087cf5;"></i></span>
                        <div>
                            <div class="service-title">Informasi Pengiriman</div>
                            <p class="service-text">
                                Informasi pengiriman mengikuti proses checkout.
                            </p>
                        </div>
                    </div>

                    <div class="service-item">
                        <span class="service-icon"><i data-lucide="shield-check" style="width: 18px; height: 18px; color: #087cf5;"></i></span>
                        <div>
                            <div class="service-title">Belanja di NexTech</div>
                            <p class="service-text">
                                Periksa detail produk sebelum melakukan pemesanan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- DESKRIPSI -->
        <section class="details-section" id="deskripsi-produk">
            <h2 class="details-heading">Deskripsi Produk</h2>

            <div class="details-box is-clamped" id="detailDesc" title="Klik untuk membaca selengkapnya">{{ $description }}</div>

            <button type="button" class="desc-toggle-btn" id="descToggle" aria-expanded="false" style="display: none;">
                <span class="toggle-text">Baca selengkapnya</span>
                <span class="toggle-arrow">▼</span>
            </button>
        </section>

        <!-- RATING & ULASAN PRODUK -->
        <section class="reviews-section" id="ulasan-produk">
            <div class="reviews-header">
                <h2 class="details-heading">Rating & Ulasan Pembeli</h2>
                <div class="rating-summary-banner">
                    <div class="big-rating-num">{{ ($averageRating ?? 0) > 0 ? number_format($averageRating, 1) : '0.0' }}</div>
                    <div>
                        <div class="big-stars" style="color: #f59e0b; font-size: 20px;">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= round($averageRating ?? 0) ? '★' : '☆' }}
                            @endfor
                        </div>
                        <div style="font-size: 13px; color: #64748b;">Rata-rata dari {{ $reviewCount ?? 0 }} ulasan pembeli</div>
                    </div>
                </div>
            </div>

            <!-- FORM TULIS ULASAN -->
            <div class="review-form-card">
                <h3 style="font-size: 16px; font-weight: 700; color: #10245c; margin-bottom: 14px;">
                    {{ $userReview ? 'Edit / Perbarui Ulasan Anda' : 'Tulis Ulasan untuk Produk Ini' }}
                </h3>

                @if(session('is_user'))
                    <form action="{{ route('products.review', $product->product_id) }}" method="POST">
                        @csrf
                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                                Beri Rating Bintang (1 - 5) *:
                            </label>
                            <div class="star-rating-selector" id="starSelector">
                                @php $currentStar = (int) old('rating', $userReview->rating ?? 5); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="star-select-item {{ $i <= $currentStar ? 'active' : '' }}" data-val="{{ $i }}" onclick="selectStarRating({{ $i }})">★</span>
                                @endfor
                                <input type="hidden" name="rating" id="ratingInput" value="{{ $currentStar }}">
                                <span id="starRatingLabel" style="font-size: 13px; font-weight: 600; color: #f59e0b; margin-left: 10px;">
                                    {{ $currentStar }} Bintang
                                </span>
                            </div>
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label for="reviewText" style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">
                                Tuliskan Pengalaman & Ulasan Produk *:
                            </label>
                            <textarea
                                id="reviewText"
                                name="review"
                                rows="3"
                                class="review-textarea"
                                placeholder="Ceritakan kepuasan Anda mengenai produk ini, fitur favorit, atau saran lainnya..."
                                required
                            >{{ old('review', $userReview->review ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 10px 22px; font-size: 14px;">
                            {{ $userReview ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                        </button>
                    </form>
                @else
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 18px; text-align: center;">
                        <p style="font-size: 14px; color: #64748b; margin-bottom: 10px;">
                            Ingin memberikan ulasan dan rating pada produk ini?
                        </p>
                        <a href="{{ route('login') }}" class="btn btn-primary" style="display: inline-block; padding: 8px 18px; font-size: 13px; text-decoration: none;">
                            Login untuk Menulis Ulasan
                        </a>
                    </div>
                @endif
            </div>

            <!-- DAFTAR ULASAN PEMBELI -->
            <div class="reviews-list-card">
                <h3 style="font-size: 16px; font-weight: 700; color: #10245c; margin-bottom: 16px;">
                    Daftar Ulasan ({{ $reviewCount ?? 0 }})
                </h3>

                @if (!isset($reviews) || $reviews->isEmpty())
                    <div style="text-align: center; padding: 36px 20px; color: #64748b;">
                        <div style="margin-bottom: 8px;"><i data-lucide="message-square" style="width: 32px; height: 32px; color: #94a3b8;"></i></div>
                        <p style="font-size: 14px;">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan.</p>
                    </div>
                @else
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach ($reviews as $rev)
                            <div class="single-review-item">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="rev-avatar">
                                            @if(!empty($rev->profile_photo))
                                                <img src="{{ asset('storage/' . $rev->profile_photo) }}" alt="{{ $rev->first_name }}">
                                            @else
                                                {{ strtoupper(substr($rev->first_name ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-size: 14px; font-weight: 700; color: #10245c;">
                                                {{ $rev->first_name }} {{ $rev->last_name }}
                                            </div>
                                            <div style="font-size: 12px; color: #94a3b8;">
                                                {{ date('d M Y', strtotime($rev->created_at ?? now())) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div style="color: #f59e0b; font-size: 15px;">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= (int) $rev->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                </div>

                                <p style="font-size: 14px; color: #475569; line-height: 1.6; margin: 0; white-space: pre-line;">
                                    {{ $rev->review }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <!-- REKOMENDASI -->
        <section class="recommendation-section">
            <div class="section-heading-row">
                <h2>Produk Serupa</h2>
                <a href="{{ route('products.index') }}">Lihat semua </a>
            </div>

            @if (isset($recommendations) && $recommendations->count())
                <div class="recommendation-grid">
                    @foreach ($recommendations as $item)
                        <a
                            href="{{ route('products.show', $item->product_id) }}"
                            class="recommendation-card"
                        >
                            <div class="recommendation-image">
                                @if (!empty($item->product_image))
                                    <img
                                        src="{{ asset('assets/images/' . $item->product_image) }}"
                                        alt="{{ $item->product_title }}"
                                        loading="lazy"
                                    >
                                @else
                                    <span class="image-placeholder">
                                        Gambar belum tersedia
                                    </span>
                                @endif
                            </div>

                            <div class="recommendation-content">
                                <p class="recommendation-category">
                                    {{ $item->cat_title ?? 'Produk NexTech' }}
                                </p>

                                <h3 class="recommendation-name">
                                    {{ $item->product_title }}
                                </h3>

                                <p class="recommendation-price">
                                    Rp{{ number_format((float) $item->product_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="empty-recommendations">
                    Belum ada produk serupa untuk ditampilkan.
                </p>
            @endif
        </section>
    </main>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-logo">Nex<span>Tech</span></div>
    <p>© {{ date('Y') }} NexTech. All Rights Reserved.</p>
</footer>

<script>
    const maxStock = {{ (int) ($product->stock ?? 0) }};
    const productId = {{ (int) $product->product_id }};
    const checkStockUrl = "{{ route('products.check-stock', $product->product_id) }}";
    const csrfToken = "{{ csrf_token() }}";

    function changeQuantity(change) {
        if (maxStock <= 0) {
            alert('Stok produk ini sedang habis dan tidak dapat dipesan.');
            return;
        }

        const input = document.getElementById('quantity');
        let currentVal = parseInt(input.value, 10) || 1;
        let newVal = currentVal + change;

        if (newVal < 1) {
            newVal = 1;
        } else if (newVal > maxStock) {
            alert('Jumlah pembelian tidak boleh melebihi stok yang tersedia (' + maxStock + ' unit).');
            newVal = maxStock;
        }

        input.value = newVal;
    }

    function changeProductImage(button) {
        const image = document.getElementById('mainProductImage');
        const fallback = document.getElementById('imageFallback');

        if (!image) return;

        image.src = button.dataset.image;
        image.style.display = 'block';

        if (fallback) fallback.style.display = 'none';

        document.querySelectorAll('.thumbnail').forEach(function (thumb) {
            thumb.classList.remove('active');
        });

        button.classList.add('active');
    }

    async function validateStockBackend(qty) {
        try {
            const response = await fetch(checkStockUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ qty: qty })
            });
            const data = await response.json();
            return data;
        } catch (error) {
            return {
                success: qty <= maxStock,
                available: maxStock > 0,
                stock: maxStock,
                message: qty > maxStock ? ('Jumlah pembelian melebihi stok yang tersedia (' + maxStock + ' unit).') : ''
            };
        }
    }

    async function buyProduct() {
        if (maxStock <= 0) {
            alert('Maaf, stok produk ini sedang habis dan tidak dapat dibeli.');
            return;
        }

        const qty = parseInt(document.getElementById('quantity').value, 10) || 1;

        if (qty > maxStock) {
            alert('Jumlah pembelian (' + qty + ' unit) melebihi stok yang tersedia (' + maxStock + ' unit).');
            return;
        }

        const isUserLoggedIn = {{ session('is_user') ? 'true' : 'false' }};
        if (!isUserLoggedIn) {
            alert('Silakan login terlebih dahulu untuk melakukan pembelian.');
            window.location.href = "{{ route('login') }}";
            return;
        }

        const result = await validateStockBackend(qty);
        if (!result.success) {
            alert(result.message || 'Stok tidak mencukupi.');
            return;
        }

        window.location.href = "{{ route('checkout') }}?product_id={{ $product->product_id }}&qty=" + qty;
    }

    async function addToCart() {
        if (maxStock <= 0) {
            alert('Maaf, stok produk ini sedang habis dan tidak dapat dimasukkan ke keranjang.');
            return;
        }

        const qty = parseInt(document.getElementById('quantity').value, 10) || 1;

        if (qty > maxStock) {
            alert('Jumlah pembelian (' + qty + ' unit) melebihi stok yang tersedia (' + maxStock + ' unit).');
            return;
        }

        const isUserLoggedIn = {{ session('is_user') ? 'true' : 'false' }};
        if (!isUserLoggedIn) {
            alert('Silakan login terlebih dahulu untuk menambahkan barang ke keranjang belanja.');
            window.location.href = "{{ route('login') }}";
            return;
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('{{ route("user.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: {{ $product->product_id }},
                    qty: qty
                })
            });

            const data = await response.json();
            if (data.success) {
                const navBadge = document.getElementById('navCartBadge');
                if (navBadge) {
                    navBadge.textContent = data.count;
                    navBadge.style.display = '';
                }
                alert(data.message || 'Berhasil menambahkan ke keranjang belanja!');
            } else {
                alert(data.message || 'Gagal menambahkan ke keranjang.');
            }
        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        }
    }

    async function toggleWishlist(productId) {
        const isUserLoggedIn = {{ session('is_user') ? 'true' : 'false' }};
        if (!isUserLoggedIn) {
            alert('Silakan login terlebih dahulu untuk menyimpan ke Wishlist.');
            window.location.href = "{{ route('login') }}";
            return;
        }

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch('{{ route("user.wishlist.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });

            const data = await response.json();
            if (data.success) {
                const btn = document.getElementById('btnWishlist');
                if (btn) {
                    const heartSvg = btn.querySelector('.heart-icon svg, .heart-icon [data-lucide="heart"]');
                    const text = btn.querySelector('.wishlist-text');
                    if (data.in_wishlist) {
                        btn.classList.add('active');
                        if (heartSvg) {
                            heartSvg.style.fill = '#ef4444';
                            heartSvg.style.stroke = '#ef4444';
                        }
                        if (text) text.textContent = 'Tersimpan';
                        btn.setAttribute('title', 'Hapus dari Wishlist');
                    } else {
                        btn.classList.remove('active');
                        if (heartSvg) {
                            heartSvg.style.fill = 'none';
                            heartSvg.style.stroke = 'currentColor';
                        }
                        if (text) text.textContent = 'Wishlist';
                        btn.setAttribute('title', 'Simpan ke Wishlist');
                    }
                }

                const navBadge = document.getElementById('navWishlistBadge');
                if (navBadge) {
                    navBadge.textContent = data.count;
                    navBadge.style.display = data.count > 0 ? '' : 'none';
                }

                alert(data.message);
            }
        } catch (err) {
            console.error(err);
            alert('Gagal memperbarui Wishlist.');
        }
    }

    function selectStarRating(ratingVal) {
        const ratingInput = document.getElementById('ratingInput');
        const starLabel = document.getElementById('starRatingLabel');
        if (ratingInput) ratingInput.value = ratingVal;
        if (starLabel) starLabel.textContent = ratingVal + ' Bintang';

        const starItems = document.querySelectorAll('.star-select-item');
        starItems.forEach(item => {
            const val = parseInt(item.getAttribute('data-val'), 10);
            if (val <= ratingVal) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    }

    // ==========================================
    // LOGIKA TOGGLE DESKRIPSI PRODUK LENGKAP
    // ==========================================
    const fullDescText = @json($description);
    const shortDescText = @json(\Illuminate\Support\Str::limit(strip_tags($description), 180));
    let isTopDescExpanded = false;

    function toggleTopDesc() {
        const topTextEl = document.getElementById('topDescText');
        const topBtnEl = document.getElementById('topDescBtn');
        if (!topTextEl || !topBtnEl) return;

        isTopDescExpanded = !isTopDescExpanded;
        if (isTopDescExpanded) {
            topTextEl.textContent = fullDescText;
            topBtnEl.textContent = 'Sembunyikan';
        } else {
            topTextEl.textContent = shortDescText;
            topBtnEl.textContent = 'Baca selengkapnya';
        }
    }

    function toggleDetailDesc() {
        const descBox = document.getElementById('detailDesc');
        const toggleBtn = document.getElementById('descToggle');
        if (!descBox || !toggleBtn) return;

        const isExpanded = descBox.classList.contains('expanded');
        if (isExpanded) {
            descBox.classList.remove('expanded');
            descBox.classList.add('is-clamped');
            descBox.setAttribute('title', 'Klik untuk membaca selengkapnya');
            toggleBtn.classList.remove('expanded');
            toggleBtn.setAttribute('aria-expanded', 'false');
            const textSpan = toggleBtn.querySelector('.toggle-text');
            if (textSpan) textSpan.textContent = 'Baca selengkapnya';

            const section = document.getElementById('deskripsi-produk');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else {
            descBox.classList.remove('is-clamped');
            descBox.classList.add('expanded');
            descBox.removeAttribute('title');
            toggleBtn.classList.add('expanded');
            toggleBtn.setAttribute('aria-expanded', 'true');
            const textSpan = toggleBtn.querySelector('.toggle-text');
            if (textSpan) textSpan.textContent = 'Tampilkan lebih sedikit';
        }
    }

    function initDescToggle() {
        const descBox = document.getElementById('detailDesc');
        const toggleBtn = document.getElementById('descToggle');
        if (!descBox || !toggleBtn) return;

        // Cek apakah konten melebihi batas 5 baris
        const isOverflowing = descBox.scrollHeight > descBox.clientHeight + 4;
        if (isOverflowing) {
            descBox.classList.add('is-clamped');
            toggleBtn.style.display = 'inline-flex';
        } else {
            descBox.classList.remove('is-clamped');
            toggleBtn.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const descBox = document.getElementById('detailDesc');
        const toggleBtn = document.getElementById('descToggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleDetailDesc);
        }

        if (descBox) {
            descBox.addEventListener('click', function() {
                if (descBox.classList.contains('is-clamped')) {
                    toggleDetailDesc();
                }
            });
        }

        initDescToggle();
        window.addEventListener('resize', initDescToggle);
    });

    window.addEventListener('load', initDescToggle);
</script>
    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')
</body>
</html> 