<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Kategori - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

</head>


<body>

<div class="admin-layout">


    @include('admin.partials.sidebar')



    <!-- MAIN -->

    <main class="main-content">


        <header class="admin-header">

            <div>

                <h1>
                    Edit Kategori
                </h1>

                <p>
                    Perbarui informasi kategori produk
                </p>

            </div>

        </header>



        <div class="dashboard-card">


            <div class="card-header">

                <div>

                    <h2>
                        Informasi Kategori
                    </h2>

                    <p>
                        ID kategori #{{ $category->cat_id }}
                    </p>

                </div>

            </div>



            <form
                action="{{ route(
                    'admin.categories.update',
                    $category->cat_id
                ) }}"
                method="POST"
            >

                @csrf

                @method('PUT')


                <div class="form-group">

                    <label>
                        Nama Kategori
                    </label>


                    <input
                        type="text"
                        name="cat_title"
                        class="form-control"
                        value="{{ old(
                            'cat_title',
                            $category->cat_title
                        ) }}"
                    >


                    @error('cat_title')

                        <div class="form-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="form-actions">

                    <a
                        href="{{ route('admin.categories') }}"
                        class="btn-secondary"
                    >

                        Batal

                    </a>


                    <button
                        type="submit"
                        class="btn-primary"
                    >

                        Simpan Perubahan

                    </button>

                </div>


            </form>


        </div>


    </main>

</div>

</body>

</html>