<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NexTech - Teknologi untuk Kebutuhanmu</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/Flexslider.css') }}">
    <style>
        /* =====================================
           BANNER CAROUSEL PROMOSI NEXTECH
        ===================================== */
        .hero.hero-full-banner {
            width: 90%;
            max-width: 1250px;
            margin: 28px auto 20px;
            padding: 0;
            border-radius: 20px;
            overflow: hidden;
            box-sizing: border-box;
            background: linear-gradient(125deg, #d4e8ff 0%, #a8d3ff 40%, #76b7ff 80%, #5ca9ff 100%);
            border: 1.5px solid #a8d2fb;
            box-shadow: 0 14px 40px rgba(8, 124, 255, 0.16), 0 4px 14px rgba(16, 36, 92, 0.08);
            position: relative;
            z-index: 2;
        }

        /* Ornamen lingkaran besar transparan khas banner di kanan atas */
        .hero.hero-full-banner::before {
            content: "";
            position: absolute;
            width: 480px;
            height: 480px;
            right: -80px;
            top: -120px;
            border-radius: 50%;
            background: 
                radial-gradient(rgba(8, 124, 255, 0.16) 1.5px, transparent 1.5px) 0 0 / 18px 18px,
                radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, rgba(255, 255, 255, 0.1) 60%, transparent 68%);
            border: 2px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 0 0 35px rgba(255, 255, 255, 0.15);
            pointer-events: none;
            z-index: 2;
        }

        /* Ornamen garis lengkung / lingkaran geometris khas banner di kiri bawah */
        .hero.hero-full-banner::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            left: 20%;
            bottom: -130px;
            border-radius: 50%;
            border: 45px solid rgba(255, 255, 255, 0.2);
            pointer-events: none;
            z-index: 2;
        }

        .hero-banner-container {
            width: 100%;
            max-width: none;
            margin: 0;
            padding: 0 0 30px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 18px;
            box-sizing: border-box;
        }

        .hero-full-banner-image {
            display: block;
            width: 100%;
            max-width: none;
            height: auto;
            max-height: none;
            object-fit: contain;
            object-position: center;
            margin: 0;
            padding: 0;
            border-radius: 20px;
        }

        .hero-banner-container .hero-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            align-self: flex-start;
            margin: 0 0 0 20%;
            flex-shrink: 0;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .hero.hero-full-banner {
                width: 92%;
                margin: 16px auto;
                border-radius: 14px;
            }

            .hero.hero-full-banner::before {
                width: 300px;
                height: 300px;
                right: -60px;
                top: -80px;
            }

            .hero.hero-full-banner::after {
                width: 200px;
                height: 200px;
                border-width: 25px;
            }

            .hero-full-banner-image {
                border-radius: 14px;
            }

            .hero-banner-container {
                gap: 14px;
                padding-bottom: 24px;
            }

            .hero-banner-container .hero-button {
                margin-left: 5%;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    @include('partials.navbar')

    <!-- HERO / BANNER -->
    <section class="hero hero-full-banner">
        <div class="flexslider" data-slider>
            <div class="slides">
                <div class="slide active">
                    <img
                        src="{{ asset('assets/images/baner_1.jfif') }}"
                        alt="Banner NexTech pertama"
                        class="slide-image"
                    >
                </div>

                <div class="slide">
                    <img
                        src="{{ asset('assets/images/baner_2.png') }}"
                        alt="Banner NexTech kedua"
                        class="slide-image"
                    >
                </div>

                <div class="slide">
                    <img
                        src="{{ asset('assets/images/baner_3.jfif') }}"
                        alt="Banner NexTech ketiga"
                        class="slide-image"
                    >
                </div>
            </div>

            <div class="slide-overlay">
                <a href="{{ route('products.index') }}" class="hero-button">
                    Lihat Produk
                </a>
            </div>

            <button type="button"
                    class="slider-arrow slider-prev"
                    aria-label="Banner sebelumnya">
                &#10094;
            </button>

            <button type="button"
                    class="slider-arrow slider-next"
                    aria-label="Banner berikutnya">
                &#10095;
            </button>

            <div class="slider-dots">
                <button type="button" class="slider-dot active" aria-label="Tampilkan banner 1"></button>
                <button type="button" class="slider-dot" aria-label="Tampilkan banner 2"></button>
                <button type="button" class="slider-dot" aria-label="Tampilkan banner 3"></button>
            </div>
        </div>
    </section>

    <!-- KATEGORI PRODUK -->
    <section class="category-section">
        <div class="category-heading">
            <h2>Kategori Produk</h2>
            <p>Pilih kategori untuk menemukan produk favoritmu.</p>
        </div>

        <div class="category-filter">
            <a href="{{ route('products.index') }}" class="category-pill active">
                Semua
            </a>

            @foreach ($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->cat_id]) }}" class="category-pill">
                    {{ $category->cat_title }}
                </a>
            @endforeach
        </div>
    </section>

    <!-- PRODUK TERBARU -->
    <section class="products-section" id="products">
        <div class="section-header">
            <div>
                <h2>Produk Terbaru</h2>
                <p>Pilihan produk terbaik untuk kebutuhan teknologi Anda.</p>
            </div>

            <a href="{{ route('products.index') }}" class="see-all">
                Lihat Semua
            </a>
        </div>

        <div class="product-grid">
            @foreach ($products as $product)
                <div class="product-card">
                    <!-- GAMBAR PRODUK -->
                    <div class="product-image">
                        <a href="{{ route('products.show', $product->product_id) }}">
                            <img
                                src="{{ asset('assets/images/' . $product->product_image) }}"
                                alt="{{ $product->product_title }}"
                                loading="lazy"
                            >
                        </a>
                    </div>

                    <!-- INFORMASI PRODUK -->
                    <div class="product-info">
                        <h3>
                            <a href="{{ route('products.show', $product->product_id) }}" style="color: inherit; text-decoration: none;">
                                {{ $product->product_title }}
                            </a>
                        </h3>

                        <p class="product-description">
                            {{ $product->product_desc }}
                        </p>

                        <div class="product-bottom">
                            <strong>
                                Rp {{ number_format($product->product_price, 0, ',', '.') }}
                            </strong>
                        </div>

                        <a href="{{ route('products.show', $product->product_id) }}" class="buy-button">
                            Detail Produk 
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- KEUNGGULAN -->
    <section class="benefits">
        <div class="benefit">
            <div class="benefit-icon"><i data-lucide="shield-check" style="width: 24px; height: 24px; color: #087cff;"></i></div>
            <div>
                <h4>Produk Original</h4>
                <p>Produk resmi dan bergaransi</p>
            </div>
        </div>

        <div class="benefit">
            <div class="benefit-icon"><i data-lucide="truck" style="width: 24px; height: 24px; color: #087cff;"></i></div>
            <div>
                <h4>Pengiriman Cepat</h4>
                <p>Barang sampai dengan aman</p>
            </div>
        </div>

        <div class="benefit">
            <div class="benefit-icon"><i data-lucide="headset" style="width: 24px; height: 24px; color: #087cff;"></i></div>
            <div>
                <h4>Customer Support</h4>
                <p>Siap membantu 24/7</p>
            </div>
        </div>

        <div class="benefit">
            <div class="benefit-icon"><i data-lucide="lock" style="width: 24px; height: 24px; color: #087cff;"></i></div>
            <div>
                <h4>Transaksi Aman</h4>
                <p>Pembayaran aman dan terpercaya</p>
            </div>
        </div>
    </section>

    <!-- TENTANG NEXTECH -->
    <section class="about" id="about">
        <h2>Tentang NexTech</h2>
        <p>
            NexTech merupakan sistem informasi penjualan produk teknologi yang menyediakan berbagai
            pilihan perangkat teknologi untuk memenuhi kebutuhan pengguna.
        </p>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-logo">
            Nex<span>Tech</span>
        </div>
        <p>
            © 2026 NexTech. All Rights Reserved.
        </p>
    </footer>

    <script src="{{ asset('assets/js/slideshow.js') }}" defer></script>
    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')
</body>

</html>
