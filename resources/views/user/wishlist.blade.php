<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexTech - Wishlist Saya</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .page-container {
            width: 90%;
            max-width: 1250px;
            margin: 36px auto 60px;
            min-height: 60vh;
        }

        .page-header {
            margin-bottom: 28px;
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

        .wishlist-card {
            position: relative;
        }

        .wishlist-remove-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #fee2e2;
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            z-index: 5;
        }

        .wishlist-remove-btn:hover {
            background: #ef4444;
            color: #ffffff;
            transform: scale(1.1);
        }

        /* EMPTY STATE */
        .empty-state {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(8, 124, 255, 0.05);
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
            <h1>Wishlist Saya</h1>
            <p>Daftar produk teknologi favorit yang Anda simpan untuk dibeli nanti.</p>
        </div>

        @if ($wishlists->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i data-lucide="heart" style="width: 48px; height: 48px; color: #94a3b8;"></i>
                </div>
                <h3>Wishlist Anda Masih Kosong</h3>
                <p>Simpan gadget dan produk teknologi idaman Anda dengan menekan ikon hati pada halaman detail produk.</p>
                <a href="{{ route('products.index') }}" class="buy-button" style="display: inline-block; width: auto; padding: 12px 28px;">
                    Jelajahi Produk
                </a>
            </div>
        @else
            <div class="product-grid">
                @foreach ($wishlists as $item)
                    <div class="product-card wishlist-card" id="wishlistCard-{{ $item->product_id }}">
                        <!-- TOMBOL HAPUS DARI WISHLIST -->
                        <button
                            type="button"
                            class="wishlist-remove-btn"
                            title="Hapus dari Wishlist"
                            onclick="removeWishlist({{ $item->product_id }})"
                        >
                            <i data-lucide="heart" style="width: 18px; height: 18px; fill: #ef4444; stroke: #ef4444;"></i>
                        </button>

                        <div class="product-image">
                            <a href="{{ route('products.show', $item->product_id) }}">
                                @if (!empty($item->product_image))
                                    <img
                                        src="{{ asset('assets/images/' . $item->product_image) }}"
                                        alt="{{ $item->product_title }}"
                                        loading="lazy"
                                    >
                                @else
                                    <span style="color:#94a3b8; font-size:12px;">Gambar belum tersedia</span>
                                @endif
                            </a>
                        </div>

                        <div class="product-info">
                            <h3>
                                <a href="{{ route('products.show', $item->product_id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $item->product_title }}
                                </a>
                            </h3>

                            @if (!empty($item->cat_title))
                                <p class="product-category-label">
                                    {{ $item->cat_title }}
                                </p>
                            @endif

                            <p class="product-description">
                                {{ $item->product_desc }}
                            </p>

                            <div class="product-bottom">
                                <strong>
                                    Rp {{ number_format($item->product_price, 0, ',', '.') }}
                                </strong>
                            </div>

                            <a href="{{ route('products.show', $item->product_id) }}" class="buy-button">
                                Detail Produk
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')

    <script>
        async function removeWishlist(productId) {
            if (!confirm('Hapus produk ini dari Wishlist Anda?')) {
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
                    const card = document.getElementById('wishlistCard-' + productId);
                    if (card) {
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        card.style.transition = 'all 0.3s ease';
                        setTimeout(() => {
                            card.remove();
                            // If empty, reload to show empty state
                            const remaining = document.querySelectorAll('.wishlist-card');
                            if (remaining.length === 0) {
                                location.reload();
                            }
                        }, 300);
                    }

                    // Update navbar badge
                    const navBadge = document.getElementById('navWishlistBadge');
                    if (navBadge) {
                        navBadge.textContent = data.count;
                        navBadge.style.display = data.count > 0 ? '' : 'none';
                    }
                }
            } catch (err) {
                console.error(err);
                alert('Gagal menghapus dari Wishlist.');
            }
        }
    </script>
</body>
</html>
