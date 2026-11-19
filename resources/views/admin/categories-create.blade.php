<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

<div class="admin-layout">

    @include('admin.partials.sidebar')

    <main class="main-content">

        <header class="admin-header">
            <div>
                <h1>Tambah Kategori</h1>
                <p>Tambahkan kategori produk baru ke katalog NexTech</p>
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
                    <h2>Informasi Kategori Baru</h2>
                    <p>Masukkan nama kategori produk di bawah ini</p>
                </div>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="cat_title" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                        Nama Kategori <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="text"
                        id="cat_title"
                        name="cat_title"
                        class="form-control"
                        value="{{ old('cat_title') }}"
                        placeholder="Contoh: Gaming Gear"
                        required
                    >

                    @error('cat_title')
                        <div class="form-error" style="color: #ef4444; font-size: 12px; margin-top: 4px;">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-actions" style="display: flex; gap: 12px; margin-top: 20px;">
                    <a href="{{ route('admin.categories') }}" class="btn-secondary" style="padding: 10px 18px; text-decoration: none;">
                        Kembali
                    </a>

                    <button type="submit" class="btn-primary" style="padding: 10px 22px;">
                        + Simpan Kategori
                    </button>
                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
