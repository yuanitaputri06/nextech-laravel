<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencarian: {{ $q ? e($q) : 'Semua Produk' }} - NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">

    <style>
        .search-results-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 36px 20px 80px;
        }

        .search-header-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 28px 32px;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.06), 0 1px 3px rgba(16, 36, 92, 0.03);
            border: 1px solid #dde7f3;
            margin-bottom: 32px;
        }

        .search-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .search-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .search-title span {
            color: #087cff;
        }

        .search-count-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background: #eff6ff;
            color: #087cff;
            font-size: 13px;
            font-weight: 600;
            border-radius: 20px;
        }

        .search-subtitle {
            margin-top: 8px;
            font-size: 14px;
            color: #64748b;
        }

        .search-empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #dde7f3;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.05);
            margin-top: 20px;
        }

        .search-empty-icon {
            font-size: 52px;
            margin-bottom: 16px;
        }

        .search-empty-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .search-empty-desc {
            font-size: 14px;
            color: #64748b;
            max-width: 480px;
            margin: 0 auto 24px;
            line-height: 1.6;
        }

        .pagination-wrapper {
            margin-top: 40px;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    @include('partials.navbar')

    <!-- KONTEN HASIL PENCARIAN -->
    <main class="search-results-page">

        <div class="search-header-box">
            <div class="search-title-row">
                <div>
                    <h1 class="search-title">
                        @if ($q !== '')
                            Hasil Pencarian: <span>"{{ $q }}"</span>
                        @else
                            Semua Produk
                        @endif
                    </h1>
                    <p class="search-subtitle">
                        Menampilkan produk yang relevan berdasarkan nama produk, kategori, atau merek.
                    </p>
                </div>

                <div class="search-count-badge">
                    {{ $totalResults }} Produk Ditemukan
                </div>
            </div>
        </div>

        @if ($products->isEmpty())
            <div class="search-empty-state">
                <div class="search-empty-icon">
                    <i data-lucide="search" style="width: 48px; height: 48px; color: #94a3b8;"></i>
                </div>
                <h2 class="search-empty-title">Produk Tidak Ditemukan</h2>
                <p class="search-empty-desc">
                    @if ($q !== '')
                        Maaf, kami tidak menemukan produk yang cocok dengan kata kunci <strong>"{{ $q }}"</strong>. Coba periksa ejaan atau gunakan kata kunci lain seperti merk, tipe produk, atau kategori gadget.
                    @else
                        Belum ada produk yang tersedia saat ini.
                    @endif
                </p>
                <a href="{{ route('products.index') }}" class="buy-button" style="display: inline-block; width: auto; padding: 12px 30px;">
                    Lihat Semua Produk
                </a>
            </div>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <div class="product-card">

                        <div class="product-image">
                            <a href="{{ route('products.show', $product->product_id) }}">
                                @if(!empty($product->product_image))
                                    <img
                                        src="{{ asset('assets/images/' . $product->product_image) }}"
                                        alt="{{ $product->product_title }}"
                                        loading="lazy"
                                    >
                                @else
                                    <span style="color:#94a3b8; font-size:12px;">Gambar belum tersedia</span>
                                @endif
                            </a>
                        </div>

                        <div class="product-info">
                            <h3>
                                <a href="{{ route('products.show', $product->product_id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $product->product_title }}
                                </a>
                            </h3>

                            @if (!empty($product->cat_title))
                                <p class="product-category-label">
                                    {{ $product->cat_title }}
                                </p>
                            @endif

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

            <div class="pagination-wrapper">
                {{ $products->links() }}
            </div>
        @endif

    </main>

    <!-- FOOTER -->
    <footer>
        <div class="footer-logo">
            Nex<span>Tech</span>
        </div>
        <p>Solusi kebutuhan perangkat teknologi terlengkap dan terpercaya.</p>
        <div class="footer-bottom">
            &copy; {{ date('Y') }} NexTech. Hak Cipta Dilindungi.
        </div>
    </footer>

    <!-- CHATBOT -->
    @include('partials.chatbot')

    <script src="{{ asset('assets/js/chatbot.js') }}"></script>
</body>
</html>
