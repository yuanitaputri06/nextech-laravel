<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - NexTech Admin</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

<div class="admin-layout">

    @include('admin.partials.sidebar')

    <main class="main-content">

        <header class="admin-header">
            <div>
                <h1>Pengaturan Sistem</h1>
                <p>Kelola kredensial akun administrator serta konfigurasi operasional toko NexTech.</p>
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

        @if(session('success'))
            <div class="alert alert-success" style="padding: 14px 18px; margin-bottom: 22px; background: #e6f9ed; border: 1px solid #b7ebd0; border-radius: 10px; color: #0d6832; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="padding: 14px 18px; margin-bottom: 22px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; color: #be123c; font-weight: 500;">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error" style="padding: 14px 18px; margin-bottom: 22px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; color: #be123c;">
                <strong>Terdapat kesalahan pada formulir:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <!-- KARTU 1: PENGATURAN AKUN ADMINISTRATOR -->
            <div class="dashboard-card" style="margin-bottom: 24px;">
                <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 14px; margin-bottom: 20px;">
                    <div>
                        <h2>👤 Profil & Kredensial Admin</h2>
                        <p>Kelola data akun yang digunakan untuk login ke Admin Panel NexTech.</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label for="admin_name" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Nama Admin <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            type="text"
                            id="admin_name"
                            name="admin_name"
                            class="form-control"
                            value="{{ old('admin_name', $admin->admin_name ?? '') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="admin_email" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Email Admin (Untuk Login) <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            type="email"
                            id="admin_email"
                            name="admin_email"
                            class="form-control"
                            value="{{ old('admin_email', $admin->admin_email ?? '') }}"
                            required
                        >
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="admin_password" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Password Baru <span style="color: #64748b; font-weight: normal;">(Biarkan kosong jika tidak ingin mengubah password)</span>
                        </label>
                        <input
                            type="password"
                            id="admin_password"
                            name="admin_password"
                            class="form-control"
                            placeholder="Masukkan password baru jika ingin diganti"
                        >
                    </div>
                </div>
            </div>

            <!-- KARTU 2: PENGATURAN INFORMASI TOKO -->
            <div class="dashboard-card" style="margin-bottom: 24px;">
                <div class="card-header" style="border-bottom: 1px solid #edf2f7; padding-bottom: 14px; margin-bottom: 20px;">
                    <div>
                        <h2>🏢 Informasi Toko & Layanan Pelanggan</h2>
                        <p>Konfigurasi profil toko online yang ditampilkan kepada pelanggan.</p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                    <div class="form-group">
                        <label for="store_name" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Nama Toko Online <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            type="text"
                            id="store_name"
                            name="store_name"
                            class="form-control"
                            value="{{ old('store_name', $storeSettings['store_name'] ?? 'NexTech Store') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="store_email" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Email Dukungan Pelanggan (CS) <span style="color: #ef4444;">*</span>
                        </label>
                        <input
                            type="email"
                            id="store_email"
                            name="store_email"
                            class="form-control"
                            value="{{ old('store_email', $storeSettings['store_email'] ?? 'support@nextech.com') }}"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="store_phone" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Nomor WhatsApp / Kontak Toko
                        </label>
                        <input
                            type="text"
                            id="store_phone"
                            name="store_phone"
                            class="form-control"
                            value="{{ old('store_phone', $storeSettings['store_phone'] ?? '+62 812-3456-7890') }}"
                        >
                    </div>

                    <div class="form-group">
                        <label for="shipping_couriers" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Daftar Ekspedisi / Kurir yang Didukung
                        </label>
                        <input
                            type="text"
                            id="shipping_couriers"
                            name="shipping_couriers"
                            class="form-control"
                            value="{{ old('shipping_couriers', $storeSettings['shipping_couriers'] ?? 'JNE, TIKI, SiCepat, GoSend') }}"
                        >
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="store_address" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Alamat Operasional Toko
                        </label>
                        <input
                            type="text"
                            id="store_address"
                            name="store_address"
                            class="form-control"
                            value="{{ old('store_address', $storeSettings['store_address'] ?? 'Jl. Teknologi Modern No. 88, Jakarta Selatan') }}"
                        >
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="store_description" style="font-weight: 600; color: #1e293b; font-size: 13px; margin-bottom: 6px; display: block;">
                            Deskripsi Singkat Toko
                        </label>
                        <textarea
                            id="store_description"
                            name="store_description"
                            rows="3"
                            class="form-control"
                            style="resize: vertical; font-family: inherit;"
                        >{{ old('store_description', $storeSettings['store_description'] ?? 'Toko e-commerce teknologi resmi terlengkap dan terpercaya.') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- TOMBOL SIMPAN -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary" style="padding: 10px 20px; text-decoration: none;">
                    Batal
                </a>
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-weight: 600;">
                    💾 Simpan Semua Pengaturan
                </button>
            </div>

        </form>

    </main>

</div>

</body>
</html>
