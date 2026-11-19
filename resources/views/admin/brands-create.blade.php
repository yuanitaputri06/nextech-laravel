<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Brand - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

</head>


<body>

<div class="admin-layout">


    @include('admin.partials.sidebar')



    <!-- MAIN -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="admin-header">

            <div>

                <h1>
                    Tambah Brand
                </h1>

                <p>
                    Tambahkan brand produk baru
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



        <!-- FORM -->

        <div class="dashboard-card">


            <div class="card-header">

                <div>

                    <h2>
                        Informasi Brand
                    </h2>

                    <p>
                        Masukkan nama brand yang ingin ditambahkan
                    </p>

                </div>

            </div>



            <form
                action="{{ route('admin.brands.store') }}"
                method="POST"
            >

                @csrf


                <!-- NAMA BRAND -->

                <div class="form-group">

                    <label>
                        Nama Brand
                    </label>


                    <input
                        type="text"
                        name="brand_title"
                        class="form-control"
                        value="{{ old('brand_title') }}"
                        placeholder="Contoh: ASUS"
                        required
                    >


                    @error('brand_title')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <!-- BUTTON -->

                <div class="form-actions">

                    <a
                        href="{{ route('admin.brands') }}"
                        class="btn-secondary"
                    >

                        Kembali

                    </a>


                    <button
                        type="submit"
                        class="btn-primary"
                    >

                        + Simpan Brand

                    </button>

                </div>


            </form>


        </div>


    </main>

</div>

</body>

</html>