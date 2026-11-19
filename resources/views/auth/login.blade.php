<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - NexTech</title>

    <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}">
</head>

<body>

    <!-- ================= NAVBAR ================= -->

    <header class="auth-navbar">

        <a href="{{ url('/') }}" class="auth-logo">
            Nex<span>Tech</span>
        </a>

       

    </header>


    <!-- ================= LOGIN ================= -->

    <main class="auth-page">

        <div class="auth-container">


            <!-- ================= BAGIAN KIRI ================= -->

            <div class="auth-left">

                <div class="auth-decoration circle-one"></div>
                <div class="auth-decoration circle-two"></div>


                <div class="auth-left-content">

                    <span class="auth-badge">
                        ✦ NexTech
                    </span>

                    <h1>
                        Teknologi untuk
                        <span>kebutuhanmu.</span>
                    </h1>

                    <p>
                        Temukan berbagai produk teknologi berkualitas
                        untuk menunjang aktivitas dan gaya hidup modern.
                    </p>


                    <!-- GAMBAR PRODUK -->

                    <div class="auth-products">

                        <div class="floating-product product-laptop">
                            <img
                                src="{{ asset('assets/images/product01.png') }}"
                                alt="Laptop"
                            >
                        </div>

                        <div class="floating-product product-phone">
                            <img
                                src="{{ asset('assets/images/product03.png') }}"
                                alt="Smartphone"
                            >
                        </div>

                        <div class="floating-product product-headphone">
                            <img
                                src="{{ asset('assets/images/product12.png') }}"
                                alt="Headphone"
                            >
                        </div>

                    </div>

                </div>

            </div>


            <!-- ================= BAGIAN KANAN ================= -->

            <div class="auth-right">

                <div class="login-card">

                    <div class="login-header">

                        <h2>
                            Selamat Datang Kembali
                        </h2>

                        <p>
                            Silakan masuk ke akun NexTech kamu.
                        </p>

                    </div>


                    <!-- FORM LOGIN -->

                    <form action="#" method="POST">

                        @csrf


                        <!-- EMAIL -->

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <div class="input-wrapper">

                                <span class="input-icon">
                                    <i data-lucide="mail" style="width: 16px; height: 16px;"></i>
                                </span>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    placeholder="Masukkan email"
                                    required
                                >

                            </div>

                        </div>


                        <!-- PASSWORD -->

                        <div class="form-group">

                            <div class="password-label">

                                <label for="password">
                                    Password
                                </label>

                                <a href="#">
                                    Lupa password?
                                </a>

                            </div>


                            <div class="input-wrapper">

                                <span class="input-icon">
                                    <i data-lucide="lock" style="width: 16px; height: 16px;"></i>
                                </span>

                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    placeholder="Masukkan password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="show-password"
                                    onclick="togglePassword()"
                                    id="showPasswordBtn"
                                >
                                    <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                                </button>

                            </div>

                        </div>


                        <!-- REMEMBER -->

                        <div class="remember">

                            <label>

                                <input
                                    type="checkbox"
                                    name="remember"
                                >

                                <span>
                                    Ingat saya
                                </span>

                            </label>

                        </div>


                        <!-- LOGIN BUTTON -->

                        <button
                            type="submit"
                            class="login-button"
                        >
                            Login
                        </button>

                    </form>


                    <!-- REGISTER -->

                    <div class="register-text">

                        Belum punya akun?

                        <a href="{{ url('/register') }}">
                            Register sekarang
                        </a>

                    </div>


                    <!-- PEMBATAS -->

                    <div class="auth-divider">

                        <span>
                            atau
                        </span>

                    </div>


                    <!-- BACK TO HOME -->

                    <a
                        href="{{ url('/') }}"
                        class="home-button"
                    >
                        Kembali ke Beranda
                    </a>

                </div>

            </div>

        </div>

    </main>


    <!-- ================= JAVASCRIPT ================= -->

    <script>

        function togglePassword() {
            const password = document.getElementById('password');
            const button = document.getElementById('showPasswordBtn');

            if (password.type === 'password') {
                password.type = 'text';
                button.innerHTML = '<i data-lucide="eye-off" style="width: 16px; height: 16px;"></i>';
            } else {
                password.type = 'password';
                button.innerHTML = '<i data-lucide="eye" style="width: 16px; height: 16px;"></i>';
            }
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }
    </script>
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