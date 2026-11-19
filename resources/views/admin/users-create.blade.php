<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah User - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

</head>


<body>

<div class="admin-layout">


    @include('admin.partials.sidebar')



    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="admin-header">

            <div>

                <h1>
                    Tambah User
                </h1>

                <p>
                    Tambahkan user baru ke NexTech
                </p>

            </div>


            <div class="admin-profile">

                <div class="profile-avatar">
                    A
                </div>

                <div>

                    <strong>
                        {{ session('admin_name') ?? 'Admin' }}
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>

        </header>



        <!-- =================================================
             FORM USER
             ================================================= -->

        <div class="dashboard-card">


            <!-- CARD HEADER -->

            <div class="card-header">

                <div>

                    <h2>
                        Informasi User
                    </h2>

                    <p>
                        Masukkan informasi user yang ingin ditambahkan
                    </p>

                </div>

            </div>



            <!-- FORM -->

            <form
                action="{{ route('admin.users.store') }}"
                method="POST"
            >

                @csrf



                <!-- NAMA DEPAN -->

                <div class="form-group">

                    <label for="first_name">
                        Nama Depan
                    </label>

                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        class="form-control"
                        value="{{ old('first_name') }}"
                        placeholder="Contoh: Budi"
                        required
                    >

                    @error('first_name')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- NAMA BELAKANG -->

                <div class="form-group">

                    <label for="last_name">
                        Nama Belakang
                    </label>

                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        class="form-control"
                        value="{{ old('last_name') }}"
                        placeholder="Contoh: Santoso"
                        required
                    >

                    @error('last_name')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        placeholder="Contoh: budi@gmail.com"
                        required
                    >

                    @error('email')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Minimal 6 karakter"
                        required
                    >

                    @error('password')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- NO HP -->

                <div class="form-group">

                    <label for="mobile">
                        No. HP
                    </label>

                    <input
                        type="text"
                        id="mobile"
                        name="mobile"
                        class="form-control"
                        value="{{ old('mobile') }}"
                        placeholder="Contoh: 081234567890"
                        required
                    >

                    @error('mobile')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- ALAMAT -->

                <div class="form-group">

                    <label for="address1">
                        Alamat
                    </label>

                    <input
                        type="text"
                        id="address1"
                        name="address1"
                        class="form-control"
                        value="{{ old('address1') }}"
                        placeholder="Masukkan alamat"
                        required
                    >

                    @error('address1')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- ALAMAT 2 -->

                <div class="form-group">

                    <label for="address2">
                        Alamat 2
                    </label>

                    <input
                        type="text"
                        id="address2"
                        name="address2"
                        class="form-control"
                        value="{{ old('address2') }}"
                        placeholder="Alamat tambahan (opsional)"
                    >

                    @error('address2')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- ACTION -->

                <div class="form-actions">


                    <a
                        href="{{ route('admin.users') }}"
                        class="btn-secondary"
                    >

                        Kembali

                    </a>


                    <button
                        type="submit"
                        class="btn-primary"
                    >

                        + Simpan User

                    </button>


                </div>


            </form>


        </div>


    </main>


</div>

</body>

</html>