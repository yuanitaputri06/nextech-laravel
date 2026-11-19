<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Produk - NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

@include('partials.navbar')

<main class="products-page">

    <div class="products-page-heading">
        <h1>
            {{ $activeCategory ? $activeCategory->cat_title : 'Semua Produk' }}
        </h1>

        <p>
            Temukan produk teknologi favoritmu di NexTech dengan jaminan kualitas dan garansi resmi.
        </p>
    </div>

    <div class="category-filter">
        <a
            href="{{ route('products.index') }}"
            class="category-filter-link {{ !$selectedCategory ? 'selected' : '' }}"
        >
            Semua
        </a>

        @foreach ($categories as $category)
            <a
                href="{{ route('products.index', ['category' => $category->cat_id]) }}"
                class="category-filter-link {{ (string) $selectedCategory === (string) $category->cat_id ? 'selected' : '' }}"
            >
                {{ $category->cat_title }}
            </a>
        @endforeach
    </div>

    @if ($products->isEmpty())
        <div class="empty-products" style="text-align: center; padding: 60px 20px;">
            <div style="margin-bottom: 16px;">
                <i data-lucide="package" style="width: 48px; height: 48px; color: #94a3b8;"></i>
            </div>
            <h3 style="font-size: 20px; color: #10245c; margin-bottom: 8px;">Produk belum tersedia</h3>
            <p style="color: #64748b; margin-bottom: 24px;">Belum ada produk dalam kategori ini.</p>

            <a href="{{ route('products.index') }}" class="buy-button" style="display: inline-block; width: auto; padding: 12px 28px;">
                Lihat Semua Produk
            </a>
        </div>
    @else

        <div class="product-grid">

            @foreach ($products as $product)
                <div class="product-card">

                    <div class="product-image">
                        <a href="{{ route('products.show', $product->product_id) }}">
                            <img
                                src="{{ asset('assets/images/' . $product->product_image) }}"
                                alt="{{ $product->product_title }}"
                                loading="lazy"
                            >
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

    @endif

</main>

<footer>
    <div class="footer-logo">Nex<span>Tech</span></div>
    <p>© 2026 NexTech. All Rights Reserved.</p>
</footer>

    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')
</body>
</html>
