<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Produk - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

    <style>

        /* =====================================================
           EDIT PRODUCT PAGE
           ===================================================== */

        .edit-content {
            padding: 25px 30px;
        }


        /* ================= BREADCRUMB ================= */

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;

            margin-bottom: 20px;

            font-size: 13px;
            color: #8a98ad;
        }

        .breadcrumb a {
            color: #087cf5;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }


        /* ================= PAGE TITLE ================= */

        .page-title {
            margin-bottom: 22px;
        }

        .page-title h2 {
            font-size: 24px;
            color: #102a56;
            margin-bottom: 5px;
        }

        .page-title p {
            font-size: 13px;
            color: #7b8ba5;
        }


        /* ================= FORM CARD ================= */

        .edit-card {
            background: white;

            border: 1px solid #e1eaf5;

            border-radius: 12px;

            box-shadow:
                0 5px 18px rgba(31, 77, 133, .05);

            overflow: hidden;

            max-width: 1000px;
        }


        /* ================= CARD HEADER ================= */

        .edit-card-header {
            padding: 20px 24px;

            border-bottom: 1px solid #edf1f6;

            display: flex;

            align-items: center;

            justify-content: space-between;
        }

        .edit-card-header h3 {
            font-size: 17px;
            color: #102a56;
        }

        .edit-card-header p {
            margin-top: 4px;

            font-size: 12px;
            color: #8997aa;
        }


        /* ================= FORM ================= */

        .edit-form {
            padding: 25px;
        }


        .form-grid-edit {
            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 20px;
        }


        .form-group-edit {
            display: flex;

            flex-direction: column;
        }


        .form-group-edit.full {
            grid-column: 1 / -1;
        }


        .form-group-edit label {
            font-size: 12px;

            font-weight: 600;

            color: #405674;

            margin-bottom: 7px;
        }


        .form-group-edit input,
        .form-group-edit select,
        .form-group-edit textarea {

            width: 100%;

            padding: 11px 13px;

            border: 1px solid #dce6f2;

            border-radius: 7px;

            background: white;

            color: #203a61;

            font-family: Arial, Helvetica, sans-serif;

            font-size: 13px;

            outline: none;

            transition: .2s ease;
        }


        .form-group-edit input:focus,
        .form-group-edit select:focus,
        .form-group-edit textarea:focus {

            border-color: #087cf5;

            box-shadow:
                0 0 0 3px rgba(8,124,245,.08);
        }


        .form-group-edit textarea {

            min-height: 120px;

            resize: vertical;

            line-height: 1.5;
        }


        .form-help {
            margin-top: 5px;

            font-size: 11px;

            color: #8997aa;
        }


        /* ================= PRODUCT ID ================= */

        .product-id-box {

            background: #f4f8fd;

            border: 1px solid #e2eaf4;

            border-radius: 7px;

            padding: 11px 13px;

            font-size: 13px;

            color: #637796;
        }


        .product-id-box strong {
            color: #102a56;
        }


        /* ================= IMAGE SECTION ================= */

        .image-section {

            display: grid;

            grid-template-columns:
                150px 1fr;

            gap: 20px;

            align-items: center;
        }


        .current-image {

            width: 150px;

            height: 130px;

            border: 1px solid #dce6f2;

            border-radius: 9px;

            background: #f8fbff;

            display: flex;

            align-items: center;

            justify-content: center;

            overflow: hidden;
        }


        .current-image img {

            width: 100%;

            height: 100%;

            object-fit: contain;

            padding: 10px;
        }


        .no-image {

            font-size: 11px;

            color: #9aa7b8;
        }


        .image-upload input[type="file"] {

            width: 100%;

            padding: 9px;

            border: 1px solid #dce6f2;

            border-radius: 7px;

            background: #f9fbfe;

            font-size: 12px;

            cursor: pointer;
        }


        .image-upload input[type="file"]::file-selector-button {

            background: #e6f1ff;

            color: #087cf5;

            border: none;

            padding: 8px 12px;

            border-radius: 5px;

            margin-right: 10px;

            cursor: pointer;

            font-size: 11px;
        }


        .image-upload input[type="file"]::file-selector-button:hover {

            background: #d5e9ff;
        }


        /* ================= ALERT ================= */

        .error-box {

            background: #ffe8e8;

            color: #c83d3d;

            border: 1px solid #ffd1d1;

            border-radius: 8px;

            padding: 12px 15px;

            margin-bottom: 20px;

            font-size: 12px;
        }


        .error-box ul {

            margin: 5px 0 0 18px;
        }


        /* ================= FORM ACTION ================= */

        .edit-actions {

            display: flex;

            align-items: center;

            justify-content: flex-end;

            gap: 10px;

            margin-top: 25px;

            padding-top: 20px;

            border-top: 1px solid #edf1f6;
        }


        .cancel-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 10px 18px;

            border-radius: 7px;

            background: #eef4fb;

            color: #45617f;

            text-decoration: none;

            font-size: 12px;

            border: none;

            transition: .2s ease;
        }


        .cancel-button:hover {

            background: #e2ebf5;

            color: #30496d;
        }


        .save-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 10px 18px;

            border-radius: 7px;

            background: #087cf5;

            color: white;

            font-size: 12px;

            border: none;

            cursor: pointer;

            transition: .2s ease;
        }


        .save-button:hover {

            background: #066bd6;

            box-shadow:
                0 5px 12px rgba(8,124,245,.20);
        }


        /* ================= RESPONSIVE ================= */

        @media (max-width: 800px) {

            .form-grid-edit {

                grid-template-columns: 1fr;
            }

            .form-group-edit.full {

                grid-column: auto;
            }

            .image-section {

                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 600px) {

            .edit-content {

                padding: 18px;
            }

            .edit-form {

                padding: 18px;
            }

            .edit-card-header {

                padding: 18px;
            }

            .edit-actions {

                flex-direction: column;

                align-items: stretch;
            }

            .cancel-button,
            .save-button {

                width: 100%;
            }

        }

    </style>

</head>


<body>


<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR
         ===================================================== -->
    @include('admin.partials.sidebar')


    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <main class="main-content">


        <div class="edit-content">


            <!-- =================================================
                 BREADCRUMB
                 ================================================= -->

            <div class="breadcrumb">

                <a href="{{ route('admin.dashboard') }}">
                    Dashboard
                </a>

                <span>›</span>

                <a href="{{ route('admin.products') }}">
                    Produk
                </a>

                <span>›</span>

                <span>
                    Edit Produk
                </span>

            </div>


            <!-- =================================================
                 TITLE
                 ================================================= -->

            <div class="page-title">

                <h2>
                    Edit Produk
                </h2>

                <p>
                    Perbarui informasi produk yang tersedia di toko.
                </p>

            </div>


            <!-- =================================================
                 VALIDATION ERROR
                 ================================================= -->

            @if($errors->any())

                <div class="error-box">

                    <strong>
                        Terdapat kesalahan pada data:
                    </strong>

                    <ul>

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <!-- =================================================
                 EDIT CARD
                 ================================================= -->

            <div class="edit-card">


                <!-- CARD HEADER -->

                <div class="edit-card-header">

                    <div>

                        <h3>
                            Informasi Produk
                        </h3>

                        <p>
                            Silakan ubah data produk sesuai kebutuhan.
                        </p>

                    </div>

                </div>


                <!-- FORM -->

                <form
                    action="{{ route(
                        'admin.products.update',
                        $product->product_id
                    ) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="edit-form"
                >

                    @csrf

                    @method('PUT')


                    <div class="form-grid-edit">


                        <!-- PRODUCT ID -->

                        <div class="form-group-edit">

                            <label>
                                ID Produk
                            </label>

                            <div class="product-id-box">

                                <strong>
                                    #{{ $product->product_id }}
                                </strong>

                            </div>

                        </div>


                        <!-- PRODUCT TITLE -->

                        <div class="form-group-edit">

                            <label for="product_title">
                                Nama Produk *
                            </label>

                            <input
                                type="text"
                                id="product_title"
                                name="product_title"
                                value="{{ old(
                                    'product_title',
                                    $product->product_title
                                ) }}"
                                placeholder="Masukkan nama produk"
                                required
                            >

                        </div>


                        <!-- BRAND -->

                        <div class="form-group-edit">

                            <label for="product_brand">
                                Brand
                            </label>

                            <input
                                type="text"
                                id="product_brand"
                                name="product_brand"
                                value="{{ old(
                                    'product_brand',
                                    $product->product_brand
                                ) }}"
                                placeholder="Contoh: ASUS, Lenovo, Samsung"
                            >

                        </div>


                        <!-- PRICE -->

                        <div class="form-group-edit">

                            <label for="product_price">
                                Harga *
                            </label>

                            <input
                                type="number"
                                id="product_price"
                                name="product_price"
                                value="{{ old(
                                    'product_price',
                                    $product->product_price
                                ) }}"
                                placeholder="Masukkan harga"
                                min="0"
                                required
                            >

                        </div>


                        <!-- STOCK -->

                        <div class="form-group-edit">

                            <label for="stock">
                                Stok Produk *
                            </label>

                            <input
                                type="number"
                                id="stock"
                                name="stock"
                                value="{{ old(
                                    'stock',
                                    $product->stock ?? 10
                                ) }}"
                                placeholder="Masukkan jumlah stok"
                                min="0"
                                required
                            >

                        </div>


                        <!-- CATEGORY -->

                        <div class="form-group-edit">

                            <label for="product_cat">
                                Kategori *
                            </label>

                            <select
                                id="product_cat"
                                name="product_cat"
                                required
                            >

                                <option value="">
                                    -- Pilih Kategori --
                                </option>

                                @foreach($categories as $category)

                                    <option
                                        value="{{ $category->cat_id }}"

                                        {{ old(
                                            'product_cat',
                                            $product->product_cat
                                        ) == $category->cat_id
                                            ? 'selected'
                                            : ''
                                        }}
                                    >

                                        {{ $category->cat_title }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <!-- KEYWORDS -->

                        <div class="form-group-edit">

                            <label for="product_keywords">
                                Keywords
                            </label>

                            <input
                                type="text"
                                id="product_keywords"
                                name="product_keywords"
                                value="{{ old(
                                    'product_keywords',
                                    $product->product_keywords
                                ) }}"
                                placeholder="Contoh: laptop, gaming, asus"
                            >

                        </div>


                        <!-- DESCRIPTION -->

                        <div class="form-group-edit full">

                            <label for="product_desc">
                                Deskripsi Produk
                            </label>

                            <textarea
                                id="product_desc"
                                name="product_desc"
                                placeholder="Masukkan deskripsi produk..."
                            >{{ old(
                                'product_desc',
                                $product->product_desc
                            ) }}</textarea>

                        </div>


                        <!-- IMAGE -->

                        <div class="form-group-edit full">

                            <label>
                                Gambar Produk
                            </label>


                            <div class="image-section">


                                <!-- GAMBAR LAMA -->

                                <div class="current-image">

                                    @if($product->product_image)

                                        <img
                                            src="{{ asset(
                                                'assets/images/' .
                                                $product->product_image
                                            ) }}"
                                            alt="{{ $product->product_title }}"
                                        >

                                    @else

                                        <span class="no-image">
                                            Belum ada gambar
                                        </span>

                                    @endif

                                </div>


                                <!-- UPLOAD -->

                                <div class="image-upload">

                                    <input
                                        type="file"
                                        name="product_image"
                                        accept=".jpg,.jpeg,.png,.webp"
                                    >

                                    <div class="form-help">

                                        Pilih gambar baru jika ingin
                                        mengganti gambar produk.
                                        Maksimal 2 MB.

                                    </div>

                                </div>


                            </div>

                        </div>


                    </div>


                    <!-- =================================================
                         BUTTON
                         ================================================= -->

                    <div class="edit-actions">


                        <a
                            href="{{ route('admin.products') }}"
                            class="cancel-button"
                        >

                            Batal

                        </a>


                        <button
                            type="submit"
                            class="save-button"
                        >

                            Simpan Perubahan

                        </button>


                    </div>


                </form>


            </div>


        </div>


    </main>


</div>


</body>

</html>