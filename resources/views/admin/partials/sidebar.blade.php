<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            N
        </div>
        <div>
            <h2>Nex<span>Tech</span></h2>
            <small>Admin Panel</small>
        </div>
    </div>

    <nav class="sidebar-menu">
        <a href="{{ route('admin.dashboard') }}"
           class="menu-item {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" class="menu-icon"></i>
            <span class="menu-text">Dashboard</span>
        </a>

        <a href="{{ route('admin.products') }}"
           class="menu-item {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
            <i data-lucide="package" class="menu-icon"></i>
            <span class="menu-text">Produk</span>
        </a>

        <a href="{{ route('admin.categories') }}"
           class="menu-item {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
            <i data-lucide="tags" class="menu-icon"></i>
            <span class="menu-text">Kategori</span>
        </a>

        <a href="{{ route('admin.brands') }}"
           class="menu-item {{ request()->routeIs('admin.brands*') ? 'active' : '' }}">
            <i data-lucide="tag" class="menu-icon"></i>
            <span class="menu-text">Brand</span>
        </a>

        <a href="{{ route('admin.users') }}"
           class="menu-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
            <i data-lucide="users" class="menu-icon"></i>
            <span class="menu-text">User</span>
        </a>

        <a href="{{ route('admin.orders') }}"
           class="menu-item {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
            <i data-lucide="shopping-bag" class="menu-icon"></i>
            <span class="menu-text">Pesanan</span>
        </a>

        <a href="{{ route('admin.reports') }}"
           class="menu-item {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <i data-lucide="chart-column" class="menu-icon"></i>
            <span class="menu-text">Laporan</span>
        </a>

        <div style="height: 1px; background: rgba(255, 255, 255, 0.08); margin: 6px 12px;"></div>

        <a href="{{ route('admin.store') }}"
           class="menu-item"
           title="Buka Tampilan Toko NexTech">
            <i data-lucide="store" class="menu-icon"></i>
            <span class="menu-text">Kembali ke Toko</span>
        </a>
    </nav>

    <div class="sidebar-bottom">
        <form action="{{ route('logout') }}" method="POST" style="margin: 0; padding: 0;">
            @csrf
            <button type="submit"
                    class="menu-item"
                    style="width: 100%; border: none; background: transparent; cursor: pointer; text-align: left; padding: 12px 14px; font-family: inherit;">
                <i data-lucide="log-out" class="menu-icon"></i>
                <span class="menu-text">Logout</span>
            </button>
        </form>
    </div>
</aside>

<script src="{{ asset('assets/js/lucide.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>
