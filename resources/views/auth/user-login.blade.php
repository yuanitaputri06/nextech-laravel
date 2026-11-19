<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Login User - NexTech
    </title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #edf7ff 0%, #e5f2ff 38%, #f3f8ff 70%, #eaf4ff 100%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: -150px;
            right: -150px;
            width: 520px;
            height: 520px;
            border-radius: 50%;
            background: 
                radial-gradient(rgba(8, 124, 255, 0.12) 1.5px, transparent 1.5px) 0 0 / 20px 20px,
                radial-gradient(circle, rgba(255, 255, 255, 0.65) 0%, rgba(220, 238, 255, 0.35) 50%, rgba(185, 221, 255, 0.12) 70%, transparent 71%);
            border: 2px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 0 0 35px rgba(255, 255, 255, 0.22), 0 0 0 75px rgba(8, 124, 255, 0.04);
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: "";
            position: fixed;
            bottom: -180px;
            left: -180px;
            width: 480px;
            height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.45) 0%, rgba(224, 242, 255, 0.25) 55%, transparent 70%);
            border: 60px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 0 70px rgba(8, 124, 255, 0.05);
            pointer-events: none;
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 1000px;
            min-height: 560px;
            background: white;
            border-radius: 25px;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            box-shadow: 0 20px 60px rgba(30, 80, 130, .12);
        }

        .login-left {
            background:
                linear-gradient(
                    135deg,
                    #e6f4ff,
                    #d4ebff
                );
            padding: 55px;
            position: relative;
            overflow: hidden;
        }

        .logo {
            font-size: 27px;
            font-weight: 800;
            color: #122b63;
            margin-bottom: 75px;
        }

        .logo span {
            color: #087cff;
        }

        .login-left h1 {
            color: #122b63;
            font-size: 42px;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .login-left h1 span {
            color: #087cff;
        }

        .login-left p {
            color: #557092;
            line-height: 1.7;
            max-width: 400px;
        }

        .login-right {
            padding: 55px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            color: #122b63;
            font-size: 30px;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #8394ae;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .alert {
            background: #fff0f0;
            border: 1px solid #ffcaca;
            color: #d82e2e;
            padding: 12px;
            border-radius: 9px;
            margin-bottom: 18px;
            font-size: 13px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: #18346d;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            height: 48px;
            padding: 0 15px;
            border: 1px solid #cdddf0;
            border-radius: 9px;
            outline: none;
            color: #18346d;
            font-size: 14px;
        }

        .form-group input:focus {
            border-color: #087cff;
            box-shadow:
                0 0 0 3px
                rgba(8, 124, 255, .08);
        }

        .login-button {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 9px;
            background: #087cff;
            color: white;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            margin-top: 5px;
        }

        .login-button:hover {
            background: #066edc;
        }

        .register-link {
            text-align: center;
            margin-top: 22px;
            color: #8191a9;
            font-size: 13px;
        }

        .register-link a {
            color: #087cff;
            font-weight: 700;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #52698d;
            font-size: 13px;
        }

        @media (max-width: 750px) {

            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-left {
                padding: 35px;
            }

            .logo {
                margin-bottom: 35px;
            }

            .login-left h1 {
                font-size: 32px;
            }

            .login-right {
                padding: 35px;
            }
        }

    </style>

</head>


<body>


<div class="login-wrapper">


    <!-- LEFT -->

    <div class="login-left">

        <div class="logo">

            Nex<span>Tech</span>

        </div>


        <h1>

            Belanja teknologi
            <br>

            <span>lebih mudah.</span>

        </h1>


        <p>

            Masuk ke akun NexTech untuk melihat
            produk terbaru dan mendapatkan pengalaman
            berbelanja yang lebih mudah.

        </p>

    </div>



    <!-- RIGHT -->

    <div class="login-right">


        <h2>

            Login

        </h2>


        <p class="login-subtitle">

            Masuk ke akun NexTech kamu.

        </p>



        @if(session('error'))

            <div class="alert">

                {{ session('error') }}

            </div>

        @endif



        <form action="{{ route('user.login.process') }}"
              method="POST">

            @csrf


            <div class="form-group">

                <label>
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    required
                >

            </div>



            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>



            <button
                type="submit"
                class="login-button">

                Login
                

            </button>


        </form>



        <div class="register-link">

            Belum punya akun?

            <a href="{{ route('register') }}">

                Register sekarang

            </a>

        </div>


        <a href="{{ url('/') }}"
           class="back-home">

            Kembali ke Beranda

        </a>


    </div>


</div>


</body>

</html>