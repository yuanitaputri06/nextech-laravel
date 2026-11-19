<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>

<body>

<div class="admin-layout">

    <!-- ================= SIDEBAR ================= -->
    @include('admin.partials.sidebar')

    <!-- ================= MAIN CONTENT ================= -->
    <main class="main-content">

        <!-- HEADER -->
        <header class="admin-header">
            <div>
                <h1>Manajemen Produk</h1>
                <p>Kelola katalog produk, stok, kategori, harga, dan informasi produk toko NexTech.</p>
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

        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert alert-success" style="padding: 12px 16px; margin-bottom: 20px; background: #e6f9ed; border: 1px solid #b7ebd0; border-radius: 8px; color: #0d6832; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="circle-check" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="padding: 12px 16px; margin-bottom: 20px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; color: #be123c; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="circle-x" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- PRODUCT CARD -->
        <section class="dashboard-card">

            <!-- CARD HEADER -->
            <div class="card-header" style="flex-wrap: wrap; gap: 14px; border-bottom: 1px solid #edf2f7; padding-bottom: 16px; margin-bottom: 20px;">
                <div>
                    <h2>Daftar Produk</h2>
                    <p>Total {{ count($products) }} produk tercatat di katalog sistem.</p>
                </div>

                <a href="{{ route('admin.products.create') }}" class="btn-primary" style="padding: 9px 18px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Tambah Produk</span>
                </a>
            </div>

            <!-- SEARCH & FILTER TOOLBAR -->
            <div style="margin-bottom: 20px; background: #f8fafc; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0;">
                <form action="{{ route('admin.products') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    
                    <!-- Search Input -->
                    <div style="flex: 1; min-width: 220px;">
                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            placeholder="Cari nama produk atau kata kunci..."
                            value="{{ request('q', '') }}"
                            style="width: 100%; padding: 8px 12px; font-size: 13px;"
                        >
                    </div>

                    <!-- Filter Kategori -->
                    @if(isset($categories) && count($categories) > 0)
                        <div style="min-width: 180px;">
                            <select name="category" class="form-control" style="width: 100%; padding: 8px 12px; font-size: 13px; cursor: pointer;" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->cat_id }}" {{ request('category') == $cat->cat_id ? 'selected' : '' }}>
                                        {{ $cat->cat_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Filter Brand -->
                    @if(isset($brands) && count($brands) > 0)
                        <div style="min-width: 160px;">
                            <select name="brand" class="form-control" style="width: 100%; padding: 8px 12px; font-size: 13px; cursor: pointer;" onchange="this.form.submit()">
                                <option value="">Semua Brand</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->brand_id }}" {{ request('brand') == $b->brand_id ? 'selected' : '' }}>
                                        {{ $b->brand_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <button type="submit" class="btn-primary" style="padding: 8px 16px; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                        <span>Filter</span>
                    </button>

                    @if(request()->filled('q') || request()->filled('category') || request()->filled('brand'))
                        <a href="{{ route('admin.products') }}" class="btn-secondary" style="padding: 8px 14px; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;" title="Hapus filter pencarian">
                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </form>
            </div>

            <!-- TABLE -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Brand</th>
                            <th>Harga</th>
                            <th>Stok & Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @forelse($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($product->product_image)
                                    <img
                                        src="{{ asset('assets/images/' . $product->product_image) }}"
                                        alt="{{ $product->product_title }}"
                                        class="table-image"
                                        style="width: 44px; height: 44px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;"
                                    >
                                @else
                                    <div style="width: 44px; height: 44px; background: #e2e8f0; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        <i data-lucide="package" style="width: 20px; height: 20px;"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong class="product-name" style="color: #102a56; display: block;">
                                        {{ $product->product_title }}
                                    </strong>
                                    <small style="color: #64748b; font-size: 11px;">ID: #{{ $product->product_id }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge" style="background: #eef2ff; color: #4338ca; font-size: 12px;">
                                    {{ $product->cat_title ?? 'Kategori #' . ($product->product_cat ?? '-') }}
                                </span>
                            </td>
                            <td>
                                <span style="color: #475569; font-size: 13px; font-weight: 500;">
                                    {{ $product->brand_title ?? 'Brand #' . ($product->product_brand ?? '-') }}
                                </span>
                            </td>
                            <td class="price" style="font-weight: 600; color: #087cf5; white-space: nowrap;">
                                Rp {{ number_format($product->product_price, 0, ',', '.') }}
                            </td>
                            <td>
                                @php $stock = (int) ($product->stock ?? 0); @endphp
                                @if($stock > 10)
                                    <span class="category-badge" style="background: #e6f9ed; color: #107e3e;">
                                        Tersedia ({{ $stock }})
                                    </span>
                                @elseif($stock >= 1)
                                    <span class="category-badge" style="background: #fef3c7; color: #b45309;">
                                        Stok Menipis ({{ $stock }})
                                    </span>
                                @else
                                    <span class="category-badge" style="background: #fee2e2; color: #b91c1c;">
                                        Habis (0)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons" style="display: flex; gap: 6px;">
                                    <!-- LIHAT DETAIL DI TOKO -->
                                    <a
                                        href="{{ route('products.show', $product->product_id) }}"
                                        target="_blank"
                                        class="btn-edit"
                                        style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;"
                                        title="Lihat Detail Produk di Halaman Toko"
                                    >
                                        <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    </a>

                                    <!-- EDIT PRODUK -->
                                    <a
                                        href="{{ route('admin.products.edit', $product->product_id) }}"
                                        class="btn-edit"
                                        title="Edit Produk"
                                    >
                                        <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                                    </a>

                                    <!-- HAPUS PRODUK -->
                                    <form
                                        action="{{ route('admin.products.destroy', $product->product_id) }}"
                                        method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk {{ addslashes($product->product_title) }}?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-delete"
                                            title="Hapus Produk"
                                        >
                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty" style="text-align: center; padding: 40px 20px;">
                                <div style="margin-bottom: 8px;">
                                    <i data-lucide="package" style="width: 36px; height: 36px; color: #94a3b8;"></i>
                                </div>
                                <h3>Tidak Ada Produk Ditemukan</h3>
                                <p style="color: #64748b; font-size: 13px;">
                                    @if(request()->filled('q') || request()->filled('category') || request()->filled('brand'))
                                        Tidak ada produk yang cocok dengan kata kunci atau filter yang Anda pilih.
                                    @else
                                        Belum ada data produk yang terdaftar di database toko.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</div>

</body>

</html>
