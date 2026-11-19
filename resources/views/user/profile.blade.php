<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexTech - Data Pribadi</title>

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <style>
        .page-container {
            width: 90%;
            max-width: 960px;
            margin: 36px auto 60px;
            min-height: 60vh;
        }

        .page-header {
            margin-bottom: 24px;
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

        /* STATS GRID */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        @media (max-width: 650px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 12px;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.05);
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 6px 20px rgba(8, 124, 255, 0.1);
            border-color: #b9d7fb;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #eff6ff;
            color: #087cff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-info .stat-val {
            font-size: 22px;
            font-weight: 750;
            color: #10245c;
            line-height: 1.1;
        }

        .stat-info .stat-label {
            font-size: 13px;
            color: #64748b;
            margin-top: 2px;
        }

        /* PROFILE CARD FORM */
        .profile-card {
            background: #ffffff;
            border: 1px solid #dde7f3;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 6px 24px rgba(8, 124, 255, 0.07), 0 2px 6px rgba(16, 36, 92, 0.03);
        }

        .profile-header-section {
            display: flex;
            align-items: center;
            gap: 24px;
            padding-bottom: 28px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .avatar-wrap {
            position: relative;
        }

        .avatar-box {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #eff6ff;
            border: 3px solid #bfdbfe;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            font-size: 38px;
            font-weight: 700;
            color: #087cff;
        }

        .upload-badge-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 6px 14px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #087cff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #cbd5e1;
            transition: all 0.2s;
        }

        .upload-badge-btn:hover {
            background: #087cff;
            color: #ffffff;
            border-color: #087cff;
        }

        .profile-user-info h2 {
            font-size: 22px;
            font-weight: 750;
            color: #10245c;
            margin-bottom: 4px;
        }

        .profile-user-info p {
            font-size: 14px;
            color: #64748b;
        }

        .section-subtitle {
            font-size: 18px;
            font-weight: 700;
            color: #10245c;
            margin-bottom: 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 650px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .form-input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 14px;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: #087cff;
            box-shadow: 0 0 0 3px rgba(8, 124, 255, 0.15);
        }

        .form-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 2px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-cancel {
            padding: 12px 24px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-save {
            padding: 12px 28px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
            background: #087cff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(8, 124, 255, 0.25);
            transition: all 0.2s;
        }

        .btn-save:hover {
            background: #066bdc;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(8, 124, 255, 0.35);
        }

        .alert-box {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
    </style>
</head>
<body>

    @include('partials.navbar')

    <div class="page-container">
        <div class="page-header">
            <h1>Data Pribadi</h1>
            <p>Kelola profil akun, kontak, dan alamat pengiriman Anda.</p>
        </div>

        @if(session('success'))
            <div class="alert-box alert-success" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="circle-check" style="width: 16px; height: 16px;"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-error" style="display: flex; align-items: center; justify-content: space-between;">
                <span style="display: flex; align-items: center; gap: 8px;">
                    <i data-lucide="circle-x" style="width: 16px; height: 16px;"></i>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:inherit; cursor:pointer;"><i data-lucide="x" style="width: 14px; height: 14px;"></i></button>
            </div>
        @endif

        <!-- STATISTIK PESANAN SAYA -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i data-lucide="package" style="width: 22px; height: 22px;"></i></div>
                <div class="stat-info">
                    <div class="stat-val">{{ $totalOrders ?? 0 }}</div>
                    <div class="stat-label">Total Pesanan</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#dcfce7; color:#15803d;"><i data-lucide="circle-check" style="width: 22px; height: 22px;"></i></div>
                <div class="stat-info">
                    <div class="stat-val">{{ $completedOrders ?? 0 }}</div>
                    <div class="stat-label">Pesanan Selesai</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background:#fef3c7; color:#b45309;"><i data-lucide="clock" style="width: 22px; height: 22px;"></i></div>
                <div class="stat-info">
                    <div class="stat-val">{{ $pendingOrders ?? 0 }}</div>
                    <div class="stat-label">Pesanan Menunggu</div>
                </div>
            </div>
        </div>

        <!-- FORM DATA PRIBADI -->
        <form class="profile-card" action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-header-section">
                <div class="avatar-wrap">
                    <label for="photo" class="avatar-box" title="Klik untuk mengubah foto profil">
                        @if (!empty($user->profile_photo))
                            <img id="photoPreview" src="{{ asset('storage/' . $user->profile_photo) }}" alt="Foto profil">
                            <span class="avatar-placeholder" id="avatarPlaceholder" hidden>
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                            </span>
                        @else
                            <img id="photoPreview" src="" alt="Pratinjau foto profil" hidden>
                            <span class="avatar-placeholder" id="avatarPlaceholder">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                            </span>
                        @endif
                    </label>

                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" style="display: none;">
                    <label for="photo" class="upload-badge-btn" style="display: inline-flex; align-items: center; gap: 4px;">
                        <i data-lucide="camera" style="width: 14px; height: 14px;"></i>
                        <span>Unggah Foto</span>
                    </label>
                </div>

                <div class="profile-user-info">
                    <h2>{{ $user->first_name }} {{ $user->last_name }}</h2>
                    <p>{{ $user->email }} &bull; {{ $user->mobile ?? 'Belum ada nomor telepon' }}</p>
                    <p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">Format foto: JPG, PNG, atau WebP (Maks. 2 MB)</p>
                    @error('photo')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <h3 class="section-subtitle">Informasi Akun & Pengiriman</h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="first_name" class="form-label">Nama Depan *</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-input"
                        value="{{ old('first_name', $user->first_name) }}"
                        required
                    >
                    @error('first_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name" class="form-label">Nama Belakang</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-input"
                        value="{{ old('last_name', $user->last_name) }}"
                    >
                    @error('last_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Alamat Email *</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input"
                        value="{{ old('email', $user->email) }}"
                        required
                    >
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="mobile" class="form-label">Nomor Telepon / WhatsApp</label>
                    <input
                        type="tel"
                        id="mobile"
                        name="mobile"
                        class="form-input"
                        placeholder="Contoh: 081234567890"
                        value="{{ old('mobile', $user->mobile) }}"
                    >
                    @error('mobile')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="address1" class="form-label">Alamat Lengkap Pengiriman (Jalan, No. Rumah, RT/RW)</label>
                    <input
                        type="text"
                        id="address1"
                        name="address1"
                        class="form-input"
                        placeholder="Contoh: Jl. Malioboro No. 12, RT 01/RW 02"
                        value="{{ old('address1', $user->address1) }}"
                    >
                    @error('address1')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group full-width">
                    <label for="address2" class="form-label">Kota, Provinsi, Kode Pos</label>
                    <input
                        type="text"
                        id="address2"
                        name="address2"
                        class="form-input"
                        placeholder="Contoh: Kota Yogyakarta, D.I. Yogyakarta, 55271"
                        value="{{ old('address2', $user->address2) }}"
                    >
                    @error('address2')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('user.dashboard') }}" class="btn-cancel">
                    Kembali
                </a>
                <button type="submit" class="btn-save">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <footer>
        <div class="footer-logo">Nex<span>Tech</span></div>
        <p>© 2026 NexTech. All Rights Reserved.</p>
    </footer>

    {{-- NEXTECH AI CHATBOT WIDGET --}}
    @include('partials.chatbot')

    <script>
        const photoInput = document.getElementById('photo');
        const photoPreview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('avatarPlaceholder');

        if (photoInput) {
            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Silakan pilih foto dengan format JPG, PNG, atau WebP.');
                    this.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto profil maksimal adalah 2 MB.');
                    this.value = '';
                    return;
                }

                photoPreview.src = URL.createObjectURL(file);
                photoPreview.hidden = false;
                if (placeholder) {
                    placeholder.hidden = true;
                }
            });
        }
    </script>
</body>
</html>
