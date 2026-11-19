<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Register - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/register.css') }}">

</head>


<body>


<div class="register-page">


    <!-- =====================================================
         BAGIAN KIRI
    ====================================================== -->

    <div class="register-left">


        <!-- LOGO -->

        <div class="brand-badge">

            ✦ Nex<span>Tech</span>

        </div>



        <!-- JUDUL -->

        <div class="left-content">

            <h1>

                Teknologi untuk

                <br>

                <span>
                    kebutuhanmu.
                </span>

            </h1>


            <p>

                Temukan berbagai produk teknologi
                berkualitas untuk menunjang aktivitas
                dan gaya hidup modern.

            </p>

        </div>



        <!-- GAMBAR PRODUK -->

        <div class="register-products">


            <img
                src="{{ asset('assets/images/product08.png') }}"
                class="register-product product-laptop"
                alt="Laptop"
            >


            <img
                src="{{ asset('assets/images/product09.png') }}"
                class="register-product product-phone"
                alt="Smartphone"
            >


            <img
                src="{{ asset('assets/images/product12.png') }}"
                class="register-product product-headphone"
                alt="Headphone"
            >


        </div>



        <!-- DEKORASI -->

        <div class="circle circle-top"></div>

        <div class="circle circle-bottom"></div>


    </div>



    <!-- =====================================================
         BAGIAN KANAN
    ====================================================== -->

    <div class="register-right">


        <div class="register-card">


            <!-- JUDUL -->

            <div class="register-header">

                <h2>
                    Register
                </h2>


                <p>
                    Isi data di bawah ini untuk membuat
                    akun baru NexTech.
                </p>

            </div>



            <!-- PESAN ERROR -->

            @if ($errors->any())

                <div class="alert-error">

                    <strong>
                        Data belum benar.
                    </strong>


                    <ul>

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif



            <!-- PESAN SUCCESS -->

            @if (session('success'))

                <div class="alert-success">

                    {{ session('success') }}

                </div>

            @endif



            <!-- FORM -->

            <form
                action="{{ route('register.process') }}"
                method="POST"
            >

                @csrf



                <!-- BARIS 1 -->

                <div class="form-row">


                    <!-- FIRST NAME -->

                    <div class="form-group">

                        <label for="first_name">
                            Nama Depan
                        </label>


                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                placeholder="Masukkan nama depan"
                                required
                            >
                        </div>
                    </div>

                    <!-- LAST NAME -->
                    <div class="form-group">
                        <label for="last_name">
                            Nama Belakang
                        </label>
                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="Masukkan nama belakang"
                                required
                            >
                        </div>
                    </div>

                </div>

                <!-- BARIS 2 -->
                <div class="form-row">

                    <!-- EMAIL -->
                    <div class="form-group">
                        <label for="email">
                            Email
                        </label>
                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan email"
                                required
                            >
                        </div>
                    </div>

                    <!-- USERNAME / MOBILE -->
                    <div class="form-group">
                        <label for="mobile">
                            No. HP
                        </label>
                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="phone" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="text"
                                id="mobile"
                                name="mobile"
                                value="{{ old('mobile') }}"
                                placeholder="Masukkan nomor telepon"
                                required
                            >
                        </div>

                    </div>


                </div>



                <!-- ALAMAT -->

                <div class="form-group full-width">

                    <label for="address1">
                        Alamat
                    </label>


                    <div class="input-box">

                        <span class="input-icon">
                            ⌂
                        </span>


                        <input
                            type="text"
                            id="address1"
                            name="address1"
                            value="{{ old('address1') }}"
                            placeholder="Masukkan alamat"
                            required
                        >

                    </div>

                </div>



                <!-- BARIS PASSWORD -->

                <div class="form-row">


                    <!-- PASSWORD -->

                    <div class="form-group">

                        <label for="password">
                            Password
                        </label>


                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Buat password"
                                required
                            >
                        </div>
                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div class="form-group">
                        <label for="password_confirmation">
                            Konfirmasi Password
                        </label>
                        <div class="input-box">
                            <span class="input-icon">
                                <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                            </span>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                required
                            >
                        </div>

                    </div>


                </div>



                <!-- TOMBOL DAFTAR -->

                <button
                    type="submit"
                    class="register-button"
                >

                    Daftar

                    <span>
                        
                    </span>

                </button>


            </form>



            <!-- LOGIN -->

            <div class="login-link">

                Sudah punya akun?

                <a href="{{ route('login') }}">
                    Login di sini
                </a>

            </div>



            <!-- PEMBATAS -->

            <div class="separator">

                <span></span>

                <p>
                    atau
                </p>

                <span></span>

            </div>



            <!-- KEMBALI KE BERANDA -->

            <a
                href="{{ url('/') }}"
                class="back-home"
            >

                <span>
                    
                </span>

                Kembali ke Beranda

            </a>


        </div>

    </div>


</div>

<script src="{{ asset('assets/js/lucide.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    });
</script>
</body>

</html>