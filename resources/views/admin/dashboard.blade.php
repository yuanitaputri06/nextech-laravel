<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
    <style>
        /* Clickable Stat Cards */
        a.stat-card {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            display: flex;
        }
        a.stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(8, 124, 255, 0.14);
            border-color: #93c5fd;
        }

        /* Period Filter Buttons */
        .period-btn {
            text-decoration: none;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 7px;
            color: #475569;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .period-btn:hover {
            background: rgba(255, 255, 255, 0.8);
            color: #087cf5;
        }
        .period-btn.active {
            background: #087cf5;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(8, 124, 255, 0.3);
        }

        /* Responsive Analytics Grid */
        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        @media (max-width: 992px) {
            .analytics-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>

<body>

<div class="admin-layout">

    <!-- ================= SIDEBAR UNIFIKASI ================= -->
    @include('admin.partials.sidebar')

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="admin-header">
            <div>
                <h1>Dashboard</h1>
                <p>Kelola data dan pantau aktivitas sistem NexTech dari dua database.</p>
            </div>

            <div class="admin-profile">
                <div class="profile-avatar">
                    {{ strtoupper(substr(session('admin_name', 'A'), 0, 1)) }}
                </div>
                <div>
                    <strong>{{ session('admin_name', 'Admin') }}</strong>
                    <small>Administrator</small>
                </div>
            </div>
        </header>

        <!-- ================= WELCOME ================= -->
        <section class="welcome-card">
            <div>
                <span class="welcome-small">Dashboard Admin</span>
                <h2>Selamat datang kembali, {{ session('admin_name', 'Admin') }} 👋</h2>
                <p>Kelola data produk, user, kategori, pesanan, dan pantau catatan aktivitas sistem secara real-time.</p>
            </div>

            <div class="welcome-products">
                <img src="{{ asset('assets/images/product08.png') }}" alt="Laptop">
                <img src="{{ asset('assets/images/product09.png') }}" alt="Smartphone">
                <img src="{{ asset('assets/images/product10.png') }}" alt="Earbuds">
                <img src="{{ asset('assets/images/product11.png') }}" alt="Smartwatch">
            </div>
        </section>

        <!-- ================= STATISTICS (CLICKABLE CARDS) ================= -->
        <section class="statistics">

            <!-- TOTAL PRODUK -->
            <a href="{{ route('admin.products') }}" class="stat-card" title="Buka Manajemen Produk">
                <div class="stat-icon blue">
                    <i data-lucide="package"></i>
                </div>
                <div>
                    <p>Total Produk</p>
                    <h2>{{ $totalProducts }}</h2>
                    <small>Produk tersedia &bull; Kelola</small>
                </div>
            </a>

            <!-- TOTAL USER -->
            <a href="{{ route('admin.users') }}" class="stat-card" title="Buka Manajemen User">
                <div class="stat-icon purple">
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <p>Total User</p>
                    <h2>{{ $totalUsers }}</h2>
                    <small>User terdaftar &bull; Kelola</small>
                </div>
            </a>

            <!-- TOTAL PESANAN -->
            <a href="{{ route('admin.orders') }}" class="stat-card" title="Buka Manajemen Pesanan">
                <div class="stat-icon green">
                    <i data-lucide="shopping-cart"></i>
                </div>
                <div>
                    <p>Total Pesanan</p>
                    <h2>{{ $totalOrders }}</h2>
                    <small>
                        <span style="color: #087cf5; font-weight: 600;">{{ $completedOrders ?? 0 }} Selesai</span> &bull; 
                        <span style="color: #f59e0b; font-weight: 600;">{{ $pendingOrders ?? 0 }} Pending</span>
                    </small>
                </div>
            </a>

            <!-- TOTAL KATEGORI -->
            <a href="{{ route('admin.categories') }}" class="stat-card" title="Buka Manajemen Kategori">
                <div class="stat-icon orange">
                    <i data-lucide="layers"></i>
                </div>
                <div>
                    <p>Total Kategori</p>
                    <h2>{{ $totalCategories }}</h2>
                    <small>Kategori produk &bull; Kelola</small>
                </div>
            </a>

        </section>

        <!-- ================= ANALISIS DAN STATISTIK PENJUALAN ================= -->
        <section style="margin-top: 24px; margin-bottom: 24px;">

            <!-- HEADER ANALISIS & FILTER PERIODE -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 18px;">
                <div>
                    <h2 style="font-size: 20px; color: #102a56; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="chart-column" style="width: 22px; height: 22px; color: #087cf5;"></i>
                        <span>Analisis dan Statistik Penjualan</span>
                    </h2>
                    <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">
                        Visualisasi metrik penjualan, tren pendapatan real-time, dan performa produk toko NexTech.
                    </p>
                </div>

                <!-- FILTER PERIODE -->
                <div class="period-filter-group" style="display: flex; gap: 6px; flex-wrap: wrap; background: #e2edf8; padding: 4px; border-radius: 9px;">
                    <a href="{{ route('admin.dashboard', ['period' => 'all']) }}" 
                       class="period-btn {{ $period === 'all' ? 'active' : '' }}">Semua</a>
                    <a href="{{ route('admin.dashboard', ['period' => 'year']) }}" 
                       class="period-btn {{ $period === 'year' ? 'active' : '' }}">Tahun Ini</a>
                    <a href="{{ route('admin.dashboard', ['period' => 'month']) }}" 
                       class="period-btn {{ $period === 'month' ? 'active' : '' }}">Bulan Ini</a>
                    <a href="{{ route('admin.dashboard', ['period' => '30days']) }}" 
                       class="period-btn {{ $period === '30days' ? 'active' : '' }}">30 Hari Terakhir</a>
                    <a href="{{ route('admin.dashboard', ['period' => '7days']) }}" 
                       class="period-btn {{ $period === '7days' ? 'active' : '' }}">7 Hari Terakhir</a>
                </div>
            </div>

            <!-- GRID GRAFIK (2 KOLOM DESKTOP, 1 KOLOM MOBILE) -->
            <div class="analytics-grid">

                <!-- A. GRAFIK PENDAPATAN BULANAN (BAR CHART) -->
                <div class="dashboard-card" style="margin-bottom: 0;">
                    <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 16px; color: #102a56; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="trending-up" style="width: 18px; height: 18px; color: #087cf5;"></i>
                                <span>Pendapatan Penjualan Bulanan</span>
                            </h3>
                            <small style="color: #64748b;">Hanya transaksi pesanan yang berhasil dibayar (Lunas)</small>
                        </div>
                        <div>
                            <span style="font-size: 12px; background: #e6f9ed; color: #107e3e; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if(count($chartRevenueLabels) > 0)
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    @else
                        <div style="height: 260px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center;">
                            <div style="margin-bottom: 8px;"><i data-lucide="wallet" style="width: 32px; height: 32px; color: #94a3b8;"></i></div>
                            <p style="margin: 0; font-size: 14px; font-weight: 500;">Belum ada pendapatan terverifikasi pada periode ini.</p>
                            <small style="color: #94a3b8;">Hanya pesanan berstatus berhasil / lunas yang dihitung sebagai pendapatan.</small>
                        </div>
                    @endif
                </div>

                <!-- B. DISTRIBUSI STATUS PEMBAYARAN (DOUGHNUT CHART) -->
                <div class="dashboard-card" style="margin-bottom: 0;">
                    <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 16px; color: #102a56; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="pie-chart" style="width: 18px; height: 18px; color: #f59e0b;"></i>
                                <span>Distribusi Status Pembayaran</span>
                            </h3>
                            <small style="color: #64748b;">Status verifikasi pembayaran aktual di database</small>
                        </div>
                        <div>
                            <span style="font-size: 12px; background: #eff6ff; color: #087cf5; font-weight: 600; padding: 5px 10px; border-radius: 6px;">
                                {{ $summary['total_orders'] }} Total Pesanan
                            </span>
                        </div>
                    </div>

                    @if($summary['total_orders'] > 0)
                        <div style="display: flex; flex-direction: column; gap: 14px;">
                            <div style="position: relative; height: 180px; width: 100%;">
                                <canvas id="paymentStatusChart"></canvas>
                            </div>

                            <!-- LEGENDA STATUS PEMBAYARAN -->
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 12px;">
                                <a href="{{ route('admin.orders', ['payment_status' => 'Completed']) }}" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; background: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0;" title="Lihat pesanan yang sudah dibayar">
                                    <span style="color: #166534; display: flex; align-items: center; gap: 6px;">
                                        <i data-lucide="circle-check" style="width: 13px; height: 13px; color: #10b981;"></i>
                                        Sudah Dibayar
                                    </span>
                                    <strong style="color: #166534;">{{ $summary['paid_orders'] }}</strong>
                                </a>
                                <a href="{{ route('admin.orders', ['payment_status' => 'Pending']) }}" style="text-decoration: none; display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; background: #fffbeb; border-radius: 6px; border: 1px solid #fde68a;" title="Lihat pesanan menunggu pembayaran">
                                    <span style="color: #92400e; display: flex; align-items: center; gap: 6px;">
                                        <i data-lucide="clock" style="width: 13px; height: 13px; color: #f59e0b;"></i>
                                        Menunggu Bayar
                                    </span>
                                    <strong style="color: #92400e;">{{ $summary['waiting_payment'] }}</strong>
                                </a>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; background: #fef2f2; border-radius: 6px; border: 1px solid #fecaca;">
                                    <span style="color: #991b1b; display: flex; align-items: center; gap: 6px;">
                                        <i data-lucide="circle-x" style="width: 13px; height: 13px; color: #ef4444;"></i>
                                        Gagal / Ditolak
                                    </span>
                                    <strong style="color: #991b1b;">{{ $summary['failed'] }}</strong>
                                </div>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 7px 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="color: #475569; display: flex; align-items: center; gap: 6px;">
                                        <i data-lucide="clock" style="width: 13px; height: 13px; color: #94a3b8;"></i>
                                        Batal / Expired
                                    </span>
                                    <strong style="color: #475569;">{{ $summary['cancelled'] }}</strong>
                                </div>
                            </div>
                        </div>
                    @else
                        <div style="height: 260px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center;">
                            <div style="margin-bottom: 8px;"><i data-lucide="shopping-cart" style="width: 32px; height: 32px; color: #94a3b8;"></i></div>
                            <p style="margin: 0; font-size: 14px;">Belum ada pesanan pada periode ini.</p>
                        </div>
                    @endif
                </div>

                <!-- C. GRAFIK PRODUK PER KATEGORI (BAR CHART) -->
                <div class="dashboard-card" style="margin-bottom: 0;">
                    <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 16px; color: #102a56; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="layers" style="width: 18px; height: 18px; color: #0284c7;"></i>
                                <span>Jumlah Produk per Kategori</span>
                            </h3>
                            <small style="color: #64748b;">Data kategori & produk aktual yang tersimpan di database</small>
                        </div>
                        <a href="{{ route('admin.categories') }}" style="font-size: 12px; color: #087cf5; text-decoration: none; font-weight: 600;">
                            Kelola Kategori
                        </a>
                    </div>

                    @if(count($chartCategoryLabels) > 0)
                        <div style="position: relative; height: 260px; width: 100%;">
                            <canvas id="categoryProductsChart"></canvas>
                        </div>
                    @else
                        <div style="height: 260px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center;">
                            <div style="margin-bottom: 8px;"><i data-lucide="layers" style="width: 32px; height: 32px; color: #94a3b8;"></i></div>
                            <p style="margin: 0; font-size: 14px;">Belum ada kategori produk di database.</p>
                        </div>
                    @endif
                </div>

                <!-- D. RINGKASAN PESANAN DAN PENDAPATAN (TABEL & METRICS) -->
                <div class="dashboard-card" style="margin-bottom: 0; display: flex; flex-direction: column;">
                    <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 12px; margin-bottom: 16px;">
                        <div>
                            <h3 style="font-size: 16px; color: #102a56; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i data-lucide="wallet" style="width: 18px; height: 18px; color: #10b981;"></i>
                                <span>Ringkasan Pesanan & Pendapatan</span>
                            </h3>
                            <small style="color: #64748b;">Pemisahan tegas antara status pembayaran dan pengiriman</small>
                        </div>
                        <a href="{{ route('admin.orders') }}" style="font-size: 12px; color: #087cf5; text-decoration: none; font-weight: 600;">
                            Kelola Pesanan
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px; flex: 1;">
                        <!-- BANNER TOTAL PENDAPATAN BERSIH -->
                        <div style="background: linear-gradient(135deg, #102f63, #087cf5); color: white; padding: 14px 18px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <small style="color: #cbd5e1; font-size: 11px; display: block; text-transform: uppercase; letter-spacing: 0.5px;">Pendapatan Transaksi Berhasil</small>
                                <strong style="font-size: 22px; letter-spacing: -0.5px;">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</strong>
                            </div>
                            <div style="opacity: 0.9;">
                                <i data-lucide="wallet" style="width: 28px; height: 28px; color: #ffffff;"></i>
                            </div>
                        </div>

                        <!-- TABEL BREAKDOWN STATUS -->
                        <div class="table-wrapper" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; color: #475569; text-align: left;">
                                        <th style="padding: 9px 12px;">Parameter Metrik</th>
                                        <th style="padding: 9px 12px; text-align: center;">Tipe Status</th>
                                        <th style="padding: 9px 12px; text-align: right;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px 12px;"><strong>Total Seluruh Pesanan</strong></td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #eef2ff; color: #4338ca; font-size: 11px;">Semua</span></td>
                                        <td style="padding: 8px 12px; text-align: right;"><strong>{{ $summary['total_orders'] }}</strong></td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px 12px;">
                                            <a href="{{ route('admin.orders', ['payment_status' => 'Completed']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Filter pesanan sudah dibayar">
                                                <i data-lucide="circle-check" style="width: 14px; height: 14px; color: #107e3e;"></i>
                                                <span>Pesanan Sudah Dibayar</span>
                                            </a>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #e6f9ed; color: #107e3e; font-size: 11px;">Pembayaran</span></td>
                                        <td style="padding: 8px 12px; text-align: right; color: #107e3e; font-weight: 600;">{{ $summary['paid_orders'] }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px 12px;">
                                            <a href="{{ route('admin.orders', ['payment_status' => 'Pending']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Filter pesanan menunggu pembayaran">
                                                <i data-lucide="clock" style="width: 14px; height: 14px; color: #b45309;"></i>
                                                <span>Menunggu Pembayaran</span>
                                            </a>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #fef3c7; color: #b45309; font-size: 11px;">Pembayaran</span></td>
                                        <td style="padding: 8px 12px; text-align: right; color: #b45309; font-weight: 600;">{{ $summary['waiting_payment'] }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px 12px;">
                                            <a href="{{ route('admin.orders', ['order_status' => 'sedang_diproses']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Filter pesanan sedang diproses">
                                                <i data-lucide="package" style="width: 14px; height: 14px; color: #0369a1;"></i>
                                                <span>Sedang Diproses</span>
                                            </a>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #e0f2fe; color: #0369a1; font-size: 11px;">Pengiriman</span></td>
                                        <td style="padding: 8px 12px; text-align: right; color: #0369a1; font-weight: 600;">{{ $summary['processing'] }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px 12px;">
                                            <a href="{{ route('admin.orders', ['order_status' => 'dikirim']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Filter pesanan dikirim">
                                                <i data-lucide="truck" style="width: 14px; height: 14px; color: #7e22ce;"></i>
                                                <span>Telah Dikirim</span>
                                            </a>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #f3e8ff; color: #7e22ce; font-size: 11px;">Pengiriman</span></td>
                                        <td style="padding: 8px 12px; text-align: right; color: #7e22ce; font-weight: 600;">{{ $summary['shipped'] }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 12px;">
                                            <a href="{{ route('admin.orders', ['order_status' => 'selesai']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Filter pesanan selesai">
                                                <i data-lucide="circle-check" style="width: 14px; height: 14px; color: #15803d;"></i>
                                                <span>Telah Selesai</span>
                                            </a>
                                        </td>
                                        <td style="padding: 8px 12px; text-align: center;"><span class="category-badge" style="background: #dcfce7; color: #15803d; font-size: 11px;">Pengiriman</span></td>
                                        <td style="padding: 8px 12px; text-align: right; color: #15803d; font-weight: 600;">{{ $summary['completed'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- ================= PRODUK TERBARU (DATABASE UTAMA: ecommerce) ================= -->
        <section class="dashboard-card">

            <div class="card-header">
                <div>
                    <h2>Produk Terbaru</h2>
                    <p>Data produk yang terakhir ditambahkan di toko online.</p>
                </div>

                <a href="{{ route('admin.products') }}" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Kelola Produk</span>
                </a>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img
                                    src="{{ asset('assets/images/' . $product->product_image) }}"
                                    class="table-image"
                                    alt="{{ $product->product_title }}"
                                >
                            </td>
                            <td><strong>{{ $product->product_title }}</strong></td>
                            <td class="price">
                                Rp {{ number_format($product->product_price, 0, ',', '.') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn-edit" title="Edit Produk">
                                    Kelola
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">Belum ada produk.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </section>

        <!-- ================= RIWAYAT AKTIVITAS ADMIN (DATABASE KEDUA: ecommerce_logs) ================= -->
        <section class="dashboard-card" style="margin-top: 24px;">

            <div class="card-header">
                <div>
                    <h2>Riwayat Aktivitas Admin</h2>
                    <p>Catatan aktivitas sistem real-time dari database log (<code>ecommerce_logs</code>).</p>
                </div>

                <div style="display: flex; gap: 8px; align-items: center;">
                    <span class="category-badge" style="background: #e6f9ed; color: #107e3e; font-weight: 600;">
                        ● Live Sync (Database 2)
                    </span>
                </div>
            </div>

            @if (!empty($logsError))
                <div style="padding: 12px 16px; margin-bottom: 16px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; color: #be123c; font-size: 14px; display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="alert-triangle" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                    <span>{{ $logsError }}</span>
                </div>
            @endif

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Admin</th>
                            <th>Aktivitas</th>
                            <th>Objek</th>
                            <th>Keterangan / Deskripsi</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse ($activityLogs as $index => $log)
                        <tr>
                            <td>
                                {{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $index + 1 }}
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div class="profile-avatar" style="width: 28px; height: 28px; font-size: 12px; line-height: 28px;">
                                        {{ strtoupper(substr($log->admin_name ?? 'A', 0, 1)) }}
                                    </div>
                                    <strong>{{ $log->admin_name ?? 'Admin #' . ($log->admin_id ?? '-') }}</strong>
                                </div>
                            </td>
                            <td>
                                @php
                                    $badgeStyle = 'background: #eef5ff; color: #087cf5;';
                                    if (str_contains($log->activity_type, 'Menambahkan') || str_contains($log->activity_type, 'CREATE')) {
                                        $badgeStyle = 'background: #e6f9ed; color: #107e3e;';
                                    } elseif (str_contains($log->activity_type, 'Menghapus') || str_contains($log->activity_type, 'DELETE')) {
                                        $badgeStyle = 'background: #fde8e8; color: #c53030;';
                                    } elseif (str_contains($log->activity_type, 'Pesanan')) {
                                        $badgeStyle = 'background: #fef3c7; color: #b45309;';
                                    }
                                @endphp
                                <span class="category-badge" style="{{ $badgeStyle }} font-weight: 600;">
                                    {{ $log->activity_type }}
                                </span>
                            </td>
                            <td>
                                <code>{{ $log->object_info ?? ($log->subject_type ? $log->subject_type . ' #' . $log->subject_id : '-') }}</code>
                            </td>
                            <td>
                                <span>{{ $log->description }}</span>
                            </td>
                            <td style="white-space: nowrap; color: #64748b; font-size: 13px;">
                                {{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">
                                Belum ada catatan aktivitas admin di database kedua.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activityLogs->hasPages())
                <div style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="font-size: 13px; color: #64748b;">
                        Menampilkan {{ $activityLogs->firstItem() ?? 0 }} - {{ $activityLogs->lastItem() ?? 0 }} dari {{ $activityLogs->total() }} catatan
                    </div>

                    <div class="pagination">
                        @if ($activityLogs->onFirstPage())
                            <span>&laquo;</span>
                        @else
                            <a href="{{ $activityLogs->previousPageUrl() }}">&laquo;</a>
                        @endif

                        @foreach ($activityLogs->getUrlRange(1, $activityLogs->lastPage()) as $page => $url)
                            @if ($page == $activityLogs->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($activityLogs->hasMorePages())
                            <a href="{{ $activityLogs->nextPageUrl() }}">&raquo;</a>
                        @else
                            <span>&raquo;</span>
                        @endif
                    </div>
                </div>
            @endif

        </section>

    </main>

</div>

<!-- SCRIPT INISIALISASI CHART.JS -->
<script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart A: Pendapatan Bulanan (Bar Chart)
    const revCanvas = document.getElementById('revenueChart');
    if (revCanvas) {
        const revCtx = revCanvas.getContext('2d');
        const revGradient = revCtx.createLinearGradient(0, 0, 0, 260);
        revGradient.addColorStop(0, 'rgba(8, 124, 255, 0.95)');
        revGradient.addColorStop(1, 'rgba(56, 189, 248, 0.35)');

        new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: @json($chartRevenueLabels),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: @json($chartRevenueData),
                    backgroundColor: revGradient,
                    borderColor: '#087cf5',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#102653',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ' Pendapatan: Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) {
                                if (val >= 1000000) {
                                    return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                                }
                                return 'Rp ' + Number(val).toLocaleString('id-ID');
                            },
                            font: { size: 11 },
                            color: '#64748b'
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#1e293b' }
                    }
                }
            }
        });
    }

    // 2. Chart B: Distribusi Status Pembayaran (Doughnut Chart)
    const payCanvas = document.getElementById('paymentStatusChart');
    if (payCanvas) {
        const payCtx = payCanvas.getContext('2d');
        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: @json($chartPaymentLabels),
                datasets: [{
                    data: @json($chartPaymentData),
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#94a3b8'],
                    hoverBackgroundColor: ['#059669', '#d97706', '#dc2626', '#64748b'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11 },
                            color: '#1e293b'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#102653',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) + '%' : '0%';
                                return ' ' + ctx.label + ': ' + ctx.raw + ' pesanan (' + pct + ')';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Chart C: Produk per Kategori (Bar Chart)
    const catCanvas = document.getElementById('categoryProductsChart');
    if (catCanvas) {
        const catCtx = catCanvas.getContext('2d');
        const catGradient = catCtx.createLinearGradient(0, 0, 0, 260);
        catGradient.addColorStop(0, 'rgba(14, 165, 233, 0.95)');
        catGradient.addColorStop(1, 'rgba(56, 189, 248, 0.35)');

        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: @json($chartCategoryLabels),
                datasets: [{
                    label: 'Jumlah Produk',
                    data: @json($chartCategoryData),
                    backgroundColor: catGradient,
                    borderColor: '#0284c7',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#102653',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + ctx.raw + ' Produk';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: { size: 11 },
                            color: '#64748b'
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.8)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#1e293b' }
                    }
                }
            }
        });
    }

    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
});
</script>

</body>

</html>
