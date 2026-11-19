<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Tambah Produk - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

    <style>

        /* ================= FORM PRODUK ================= */

        .product-form {
            margin-top: 10px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;

            font-size: 13px;
            font-weight: 600;

            color: #243b63;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {

            width: 100%;

            padding: 12px 14px;

            border: 1px solid #dce5f1;

            border-radius: 8px;

            background: #fff;

            color: #243b63;

            font-family: Arial, Helvetica, sans-serif;

            font-size: 13px;

            outline: none;

            transition: .2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {

            border-color: #087cf5;

            box-shadow:
                0 0 0 3px rgba(8, 124, 245, .08);
        }

        .form-group textarea {

            resize: vertical;

            min-height: 130px;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {

            color: #9aa8bb;
        }


        /* ================= UPLOAD GAMBAR ================= */

        .image-upload {

            border: 1px dashed #cbd8e8;

            border-radius: 10px;

            padding: 20px;

            background: #f8fbff;
        }

        .image-upload p {

            margin-top: 7px;

            color: #8796aa;

            font-size: 11px;
        }


        /* ================= BUTTON ================= */

        .form-actions {

            display: flex;

            gap: 10px;

            margin-top: 10px;

            padding-top: 20px;

            border-top: 1px solid #edf1f6;
        }

        .btn-save {

            border: none;

            background: #087cf5;

            color: white;

            padding: 11px 18px;

            border-radius: 7px;

            font-size: 12px;

            font-weight: 600;

            cursor: pointer;

            transition: .2s;
        }

        .btn-save:hover {

            background: #066bd6;
        }

        .btn-cancel {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 11px 18px;

            background: #f1f5fa;

            color: #50627e;

            border-radius: 7px;

            text-decoration: none;

            font-size: 12px;

            font-weight: 600;
        }

        .btn-cancel:hover {

            background: #e5ebf3;
        }


        /* ================= ERROR ================= */

        .alert-error {

            background: #fff0f0;

            border: 1px solid #ffd2d2;

            color: #d63333;

            padding: 12px 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 12px;
        }

        .alert-error ul {

            margin: 6px 0 0 18px;
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 750px) {

            .form-grid {

                grid-template-columns: 1fr;
            }

            .form-group.full {

                grid-column: auto;
            }

        }

    </style>

</head>


<body>


<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->
    @include('admin.partials.sidebar')



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="admin-header">


            <div>

                <h1>
                    Tambah Produk
                </h1>

                <p>
                    Tambahkan produk baru ke dalam database NexTech.
                </p>

            </div>


            <!-- PROFILE -->

            <div class="admin-profile">

                <div class="profile-avatar">
                    A
                </div>

                <div>

                    <strong>
                        Admin
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>


        </header>



        <!-- =================================================
             CARD FORM
        ================================================== -->

        <section class="dashboard-card">


            <!-- CARD HEADER -->

            <div class="card-header">


                <div>

                    <h2>
                        Form Tambah Produk
                    </h2>

                    <p>
                        Isi informasi produk dengan lengkap sebelum menyimpan.
                    </p>

                </div>


                <a href="{{ route('admin.products') }}"
                   class="btn-primary">

                    Kembali

                </a>


            </div>



            <!-- =================================================
                 ERROR VALIDATION
            ================================================== -->

            @if ($errors->any())

                <div class="alert-error">

                    <strong>
                        Data belum dapat disimpan.
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



            <!-- =================================================
                 FORM
            ================================================== -->

            <form action="{{ route('admin.products.store') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="product-form">

                @csrf


                <div class="form-grid">


                    <!-- ================================
                         NAMA PRODUK
                    ================================= -->

                    <div class="form-group">

                        <label for="product_title">
                            Nama Produk
                        </label>

                        <input
                            type="text"
                            id="product_title"
                            name="product_title"
                            value="{{ old('product_title') }}"
                            placeholder="Contoh: ASUS VivoBook"
                            required
                        >

                    </div>



                    <!-- ================================
                         BRAND
                    ================================= -->

                    <div class="form-group">

                        <label for="product_brand">
                            Brand
                        </label>

                        <input
                            type="text"
                            id="product_brand"
                            name="product_brand"
                            value="{{ old('product_brand') }}"
                            placeholder="Contoh: ASUS"
                        >

                    </div>



                    <!-- ================================
                         KATEGORI
                    ================================= -->

                    <div class="form-group">

                        <label for="product_cat">
                            Kategori
                        </label>

                        <select
                            name="product_cat"
                            id="product_cat"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>


                            @foreach ($categories as $category)

                                <option
                                    value="{{ $category->cat_id }}"
                                    {{ old('product_cat') == $category->cat_id ? 'selected' : '' }}
                                >

                                    {{ $category->cat_title }}

                                </option>

                            @endforeach


                        </select>

                    </div>



                    <!-- ================================
                         HARGA
                    ================================= -->

                    <div class="form-group">

                        <label for="product_price">
                            Harga Produk
                        </label>

                        <input
                            type="number"
                            id="product_price"
                            name="product_price"
                            value="{{ old('product_price') }}"
                            placeholder="Contoh: 7500000"
                            min="0"
                            required
                        >

                    </div>



                    <!-- ================================
                         STOK PRODUK
                    ================================= -->

                    <div class="form-group">

                        <label for="stock">
                            Stok Produk
                        </label>

                        <input
                            type="number"
                            id="stock"
                            name="stock"
                            value="{{ old('stock', 10) }}"
                            placeholder="Default: 10"
                            min="0"
                        >

                    </div>



                    <!-- ================================
                         GAMBAR
                    ================================= -->

                    <div class="form-group full">

                        <label for="product_image">
                            Gambar Produk
                        </label>


                        <div class="image-upload">

                            <input
                                type="file"
                                id="product_image"
                                name="product_image"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                            >

                            <p>
                                Format yang didukung: JPG, JPEG, PNG, atau WEBP.
                            </p>

                        </div>


                    </div>



                    <!-- ================================
                         DESKRIPSI
                    ================================= -->

                    <div class="form-group full">

                        <label for="product_desc">
                            Deskripsi Produk
                        </label>

                        <textarea
                            id="product_desc"
                            name="product_desc"
                            placeholder="Masukkan deskripsi produk..."
                            required
                        >{{ old('product_desc') }}</textarea>

                    </div>



                    <!-- ================================
                         KEYWORDS
                    ================================= -->

                    <div class="form-group full">

                        <label for="product_keywords">
                            Keywords
                        </label>

                        <input
                            type="text"
                            id="product_keywords"
                            name="product_keywords"
                            value="{{ old('product_keywords') }}"
                            placeholder="Contoh: laptop, asus, vivobook, notebook"
                        >

                    </div>


                </div>



                <!-- =================================================
                     BUTTON
                ================================================== -->

                <div class="form-actions">


                    <button
                        type="submit"
                        class="btn-save"
                    >

                        + Simpan Produk

                    </button>


                    <a
                        href="{{ route('admin.products') }}"
                        class="btn-cancel"
                    >

                        Batal

                    </a>


                </div>


            </form>


        </section>


    </main>


</div>


</body>

</html>