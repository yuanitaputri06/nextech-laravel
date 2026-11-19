<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - NexTech</title>
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}">
</head>
<body>

<div class="admin-layout">

    @include('admin.partials.sidebar')

    <main class="main-content">

        <header class="admin-header">
            <div>
                <h1>Edit User</h1>
                <p>Perbarui informasi akun pelanggan NexTech</p>
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
                    <h2>Informasi User #{{ $user->user_id }}</h2>
                    <p>Ubah data user di bawah ini. Kosongkan password jika tidak ingin mengubahnya.</p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-error" style="padding: 12px 16px; margin-bottom: 20px; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; color: #be123c;">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- NAMA DEPAN -->
                <div class="form-group">
                    <label for="first_name">Nama Depan</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-control"
                        value="{{ old('first_name', $user->first_name) }}"
                        placeholder="Contoh: Budi"
                        required
                    >
                    @error('first_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NAMA BELAKANG -->
                <div class="form-group">
                    <label for="last_name">Nama Belakang</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-control"
                        value="{{ old('last_name', $user->last_name) }}"
                        placeholder="Contoh: Santoso"
                    >
                    @error('last_name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- EMAIL -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                        placeholder="Contoh: budi@gmail.com"
                        required
                    >
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- PASSWORD BARU (OPSIONAL) -->
                <div class="form-group">
                    <label for="password">Password Baru <small style="color: #64748b; font-weight: normal;">(Kosongkan jika tidak ingin mengubah password)</small></label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimal 6 karakter"
                    >
                    @error('password')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NO HP -->
                <div class="form-group">
                    <label for="mobile">No. HP</label>
                    <input
                        type="text"
                        id="mobile"
                        name="mobile"
                        class="form-control"
                        value="{{ old('mobile', $user->mobile) }}"
                        placeholder="Contoh: 081234567890"
                    >
                    @error('mobile')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ALAMAT 1 -->
                <div class="form-group">
                    <label for="address1">Alamat Utama</label>
                    <input
                        type="text"
                        id="address1"
                        name="address1"
                        class="form-control"
                        value="{{ old('address1', $user->address1) }}"
                        placeholder="Contoh: Jl. Sudirman No. 12"
                    >
                    @error('address1')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ALAMAT 2 -->
                <div class="form-group">
                    <label for="address2">Alamat Tambahan / Kota</label>
                    <input
                        type="text"
                        id="address2"
                        name="address2"
                        class="form-control"
                        value="{{ old('address2', $user->address2) }}"
                        placeholder="Contoh: Jakarta Pusat"
                    >
                    @error('address2')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.users') }}" class="btn-secondary">
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
