@php
    $isAdmin = session('is_admin');
    $isUser = session('is_user') && !$isAdmin;
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $userCartCount = 0;
    $userWishlistCount = 0;
    $navUser = null;

    if ($isUser && session('user_id')) {
        $navUser = \Illuminate\Support\Facades\DB::table('user_info')
            ->where('user_id', session('user_id'))
            ->first();
        $userCartCount = (int) \Illuminate\Support\Facades\DB::table('cart')
            ->where('user_id', session('user_id'))
            ->sum('qty');
        $userWishlistCount = (int) \Illuminate\Support\Facades\DB::table('wishlists')
            ->where('user_id', session('user_id'))
            ->count();
    }
@endphp

<!-- NAVBAR -->
<nav class="navbar">
    <div class="nav-container">

        <!-- LOGO & ADMIN IDENTITY (Sebelah Kiri) -->
        <div style="display: flex; align-items: center; gap: 14px;">
            <a href="{{ $isAdmin ? route('home') : ($isUser ? route('user.dashboard') : url('/')) }}" class="logo">
                Nex<span>Tech</span>
            </a>

            @if ($isAdmin)
                <!-- IDENTITAS AKUN ADMIN PADA STOREFRONT -->
                <div class="admin-storefront-badge" style="display: flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.08); padding: 5px 12px; border-radius: 20px; border: 1px solid rgba(56, 189, 248, 0.35);">
                    <div style="width: 24px; height: 24px; border-radius: 50%; background: #087cf5; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0;">
                        {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
                    </div>
                    <div style="display: flex; flex-direction: column; line-height: 1.15;">
                        <span style="font-size: 12px; font-weight: 600; color: #ffffff;">{{ session('admin_name', 'Admin') }}</span>
                        <span style="font-size: 9px; color: #38bdf8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Administrator</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- MENU TENGAH (HANYA: BERANDA, PRODUK, TENTANG) -->
        <div class="nav-menu" id="navMenu">
            @if ($isAdmin)
                <a href="{{ url('/') }}" class="{{ $currentRoute === 'home' || request()->is('/') ? 'active' : '' }}">
                    Beranda
                </a>
                <a href="{{ url('/products') }}" class="{{ $currentRoute === 'products.index' ? 'active' : '' }}">
                    Produk
                </a>
                <a href="{{ url('/#about') }}">
                    Tentang
                </a>
            @elseif ($isUser)
                <a href="{{ route('user.dashboard') }}" class="{{ in_array($currentRoute, ['user.dashboard', 'home']) ? 'active' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('products.index') }}" class="{{ $currentRoute === 'products.index' ? 'active' : '' }}">
                    Produk
                </a>
                <a href="{{ url('/#about') }}">
                    Tentang
                </a>
            @else
                <a href="{{ url('/') }}" class="{{ $currentRoute === 'home' || request()->is('/') ? 'active' : '' }}">
                    Beranda
                </a>
                <a href="{{ url('/products') }}" class="{{ $currentRoute === 'products.index' ? 'active' : '' }}">
                    Produk
                </a>
                <a href="{{ url('/#about') }}">
                    Tentang
                </a>
            @endif
        </div>

        <!-- SEBELAH KANAN -->
        <div class="nav-right">

            <!-- KOLOM PENCARIAN TERPADU -->
            <div class="nav-search-wrapper" id="navSearchWrapper">
                <form action="{{ route('search') }}" method="GET" class="nav-search-form" id="navSearchForm">
                    <span class="nav-search-icon">
                        <i data-lucide="search" style="width: 15px; height: 15px;"></i>
                    </span>
                    <input
                        type="text"
                        name="q"
                        id="navSearchInput"
                        class="nav-search-input"
                        placeholder="Cari produk atau kategori..."
                        value="{{ request()->routeIs('search') ? request('q') : '' }}"
                        autocomplete="off"
                    >
                    <button type="button" class="nav-search-clear" id="navSearchClear" title="Hapus pencarian" style="display: {{ request()->filled('q') ? 'flex' : 'none' }};">
                        <i data-lucide="x" style="width: 13px; height: 13px;"></i>
                    </button>
                </form>

                <!-- DROPDOWN HASIL CEPAT -->
                <div class="nav-search-dropdown" id="navSearchDropdown" style="display: none;">
                    <div class="nav-search-loading" id="navSearchLoading" style="display: none;">
                        <span>Mencari produk...</span>
                    </div>
                    <div class="nav-search-results" id="navSearchResults"></div>
                    <a href="#" class="nav-search-view-all" id="navSearchViewAll" style="display: none;">
                        Lihat semua hasil pencarian
                    </a>
                </div>
            </div>

            @if ($isAdmin)
                <!-- AKSES DASHBOARD ADMIN DARI STOREFRONT (TANPA LOGOUT) -->
                <a href="{{ route('admin.dashboard') }}" class="admin-store-dash-btn" style="display: inline-flex; align-items: center; gap: 7px; background: #087cf5; color: #ffffff; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 14px rgba(8, 124, 255, 0.35);">
                    <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                    <span>Dashboard Admin</span>
                </a>
            @elseif ($isUser)
                <!-- IKON WISHLIST -->
                <a href="{{ route('user.wishlist') }}" class="nav-icon-btn" title="Wishlist">
                    <i data-lucide="heart" style="width: 18px; height: 18px;"></i>
                    <span class="nav-badge" id="navWishlistBadge" style="{{ $userWishlistCount > 0 ? '' : 'display:none;' }}">
                        {{ $userWishlistCount }}
                    </span>
                </a>

                <!-- IKON KERANJANG -->
                <a href="{{ route('user.orders', ['tab' => 'cart']) }}" class="nav-icon-btn" title="Keranjang Belanja">
                    <i data-lucide="shopping-cart" style="width: 18px; height: 18px;"></i>
                    <span class="nav-badge" id="navCartBadge" style="{{ $userCartCount > 0 ? '' : 'display:none;' }}">
                        {{ $userCartCount }}
                    </span>
                </a>

                <!-- PROFIL USER DROPDOWN (HANYA: DATA PRIBADI & LOGOUT) -->
                <div class="user-nav-profile" id="userProfileToggle" onclick="toggleUserDropdown(event)">
                    <div class="user-avatar-circle">
                        @if (!empty($navUser->profile_photo))
                            <img src="{{ asset('storage/' . $navUser->profile_photo) }}" alt="{{ session('user_name') }}">
                        @else
                            {{ strtoupper(substr(session('user_name') ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <span class="user-nav-name">{{ session('user_name') ?? 'Pengguna' }}</span>

                    <!-- Dropdown Box -->
                    <div class="user-dropdown-menu" id="userDropdownMenu">
                        <div class="user-dropdown-header">
                            <div class="dropdown-user-name">{{ session('user_name') }}</div>
                            <div class="dropdown-user-email">{{ session('user_email') }}</div>
                        </div>

                        <a href="{{ route('user.profile') }}" class="user-dropdown-item">
                            <i data-lucide="contact-round" style="width: 16px; height: 16px; color: #087cff;"></i>
                            <span>Data Pribadi</span>
                        </a>

                        <div class="user-dropdown-divider"></div>

                        <form action="{{ route('user.logout') }}" method="POST" style="margin: 0; padding: 0;">
                            @csrf
                            <button type="submit" class="user-dropdown-item logout-btn">
                                <i data-lucide="log-out" style="width: 16px; height: 16px; color: #ef4444;"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ url('/login') }}" class="login-btn">
                    Login
                </a>
                <a href="{{ url('/register') }}" class="register-btn">
                    Register
                </a>
            @endif

            <!-- Mobile Hamburger Button -->
            <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Menu" onclick="toggleMobileMenu(event)">
                <i data-lucide="menu" style="width: 20px; height: 20px;"></i>
            </button>
        </div>

    </div>
</nav>

<!-- LUCIDE ICONS LIBRARY -->
<script src="{{ asset('assets/js/lucide.min.js') }}"></script>
<script>
    // Initialize Lucide Icons
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });

    // Toggle User Profile Dropdown
    function toggleUserDropdown(e) {
        e.stopPropagation();
        const profileToggle = document.getElementById('userProfileToggle');
        const menu = document.getElementById('userDropdownMenu');
        if (menu) {
            menu.classList.toggle('show');
            if (profileToggle) profileToggle.classList.toggle('active');
        }
    }

    // Toggle Mobile Navigation
    function toggleMobileMenu(e) {
        e.stopPropagation();
        const navMenu = document.getElementById('navMenu');
        if (navMenu) {
            navMenu.classList.toggle('mobile-open');
        }
    }

    // Live Search Logic with Debounce
    (function() {
        const searchInput = document.getElementById('navSearchInput');
        const searchClear = document.getElementById('navSearchClear');
        const searchDropdown = document.getElementById('navSearchDropdown');
        const searchResults = document.getElementById('navSearchResults');
        const searchLoading = document.getElementById('navSearchLoading');
        const searchViewAll = document.getElementById('navSearchViewAll');
        let searchTimeout = null;

        if (!searchInput) return;

        function updateClearBtn() {
            if (searchClear) {
                searchClear.style.display = searchInput.value.trim().length > 0 ? 'flex' : 'none';
            }
        }

        searchInput.addEventListener('input', function() {
            updateClearBtn();
            const q = this.value.trim();

            clearTimeout(searchTimeout);

            if (q.length < 2) {
                if (searchDropdown) searchDropdown.style.display = 'none';
                return;
            }

            if (searchLoading) searchLoading.style.display = 'block';
            if (searchDropdown) searchDropdown.style.display = 'block';
            if (searchResults) searchResults.innerHTML = '';
            if (searchViewAll) searchViewAll.style.display = 'none';

            searchTimeout = setTimeout(function() {
                fetch('{{ route("api.search") }}?q=' + encodeURIComponent(q))
                    .then(response => response.json())
                    .then(data => {
                        if (searchLoading) searchLoading.style.display = 'none';

                        if (!Array.isArray(data) || data.length === 0) {
                            if (searchResults) {
                                searchResults.innerHTML = '<div class="nav-search-empty-msg">Produk tidak ditemukan</div>';
                            }
                            if (searchViewAll) searchViewAll.style.display = 'none';
                            return;
                        }

                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="nav-search-dropdown-item">
                                    <img src="${item.image}" alt="${item.title}" class="nav-search-thumb" onerror="this.src='{{ asset('assets/images/product01.png') }}'">
                                    <div class="nav-search-info">
                                        <div class="nav-search-title">${item.title}</div>
                                        <div class="nav-search-cat">${item.category}${item.brand ? ' &bull; ' + item.brand : ''}</div>
                                    </div>
                                    <div class="nav-search-price">${item.price}</div>
                                </a>
                            `;
                        });

                        if (searchResults) searchResults.innerHTML = html;
                        if (searchViewAll) {
                            searchViewAll.href = '{{ route("search") }}?q=' + encodeURIComponent(q);
                            searchViewAll.style.display = 'block';
                        }
                    })
                    .catch(err => {
                        if (searchLoading) searchLoading.style.display = 'none';
                        if (searchResults) {
                            searchResults.innerHTML = '<div class="nav-search-empty-msg">Terjadi kesalahan memuat hasil</div>';
                        }
                    });
            }, 300);
        });

        if (searchClear) {
            searchClear.addEventListener('click', function() {
                searchInput.value = '';
                updateClearBtn();
                if (searchDropdown) searchDropdown.style.display = 'none';
                searchInput.focus();
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdownMenu');
            const profileToggle = document.getElementById('userProfileToggle');
            if (menu && menu.classList.contains('show') && !e.target.closest('#userProfileToggle')) {
                menu.classList.remove('show');
                if (profileToggle) profileToggle.classList.remove('active');
            }

            if (searchDropdown && !e.target.closest('#navSearchWrapper')) {
                searchDropdown.style.display = 'none';
            }

            const navMenu = document.getElementById('navMenu');
            if (navMenu && navMenu.classList.contains('mobile-open') && !e.target.closest('#navMenu') && !e.target.closest('#mobileMenuToggle')) {
                navMenu.classList.remove('mobile-open');
            }
        });
    })();
</script>
