<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

<div class="admin-layout">

    @include('admin.partials.sidebar')

    <main class="main-content">

        <header class="admin-header">
            <div>
                <h1>Edit Brand</h1>
                <p>Perbarui informasi brand produk</p>
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

        <div class="dashboard-card">

            <div class="card-header">
                <div>
                    <h2>Edit Brand #{{ $brand->brand_id }}</h2>
                    <p>Ubah nama brand produk di bawah ini</p>
                </div>
            </div>

            <form action="{{ route('admin.brands.update', $brand->brand_id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Brand</label>
                    <input
                        type="text"
                        name="brand_title"
                        class="form-control"
                        value="{{ old('brand_title', $brand->brand_title) }}"
                        placeholder="Contoh: ASUS"
                        required
                    >

                    @error('brand_title')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.brands') }}" class="btn-secondary">
                        Kembali
                    </a>

                    <button type="submit" class="btn-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i data-lucide="save" style="width: 15px; height: 15px;"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
