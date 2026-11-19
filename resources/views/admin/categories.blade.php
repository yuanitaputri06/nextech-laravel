<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manajemen Kategori - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

</head>


<body>

<div class="admin-layout">


    @include('admin.partials.sidebar')



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">


        <!-- HEADER -->

        <header class="admin-header">


            <div class="header-left">

                <div>

                    <h1>
                        Manajemen Kategori
                    </h1>

                    <p>
                        Kelola kategori produk NexTech
                    </p>

                </div>

            </div>


            <!-- PROFILE -->

            <div class="admin-profile">

                <div class="profile-avatar">
                    A
                </div>

                <div>

                    <strong>
                        {{ session('admin_name', 'Admin') }}
                    </strong>

                    <small>
                        Administrator
                    </small>

                </div>

            </div>


        </header>



        <!-- =================================================
             ALERT
        ================================================== -->

        @if(session('success'))

            <div class="alert alert-success">

                {{ session('success') }}

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-error" style="display: flex; align-items: center; gap: 8px;">

                <i data-lucide="circle-x" style="width: 16px; height: 16px; flex-shrink: 0;"></i>
                <span>{{ session('error') }}</span>

            </div>

        @endif



        <!-- =================================================
             CATEGORY CARD
        ================================================== -->

        <section class="dashboard-card">


            <!-- HEADER -->

            <div class="product-header">


                <div class="product-title">

                    <h2>
                        Data Kategori
                    </h2>

                    <p>
                        Daftar kategori produk yang tersedia
                    </p>

                </div>


                <a
                    href="{{ route('admin.categories.create') }}"
                    class="btn-primary"
                    style="display: inline-flex; align-items: center; gap: 6px;"
                >
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Tambah Kategori</span>
                </a>


            </div>



            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="table-wrapper">


                <table>


                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                ID
                            </th>

                            <th>
                                Nama Kategori
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    @forelse(
                        $categories
                        as $index => $category
                    )


                        <tr>


                            <!-- NOMOR -->

                            <td>
                                {{ $index + 1 }}
                            </td>


                            <!-- ID -->

                            <td>

                                <span class="category-id">
                                    #{{ $category->cat_id }}
                                </span>

                            </td>


                            <!-- NAMA -->

                            <td>

                                <strong class="product-name">

                                    <a href="{{ route('admin.products', ['category' => $category->cat_id]) }}" style="color: #102a56; text-decoration: none;" title="Lihat daftar produk di kategori ini">
                                        {{ $category->cat_title }}
                                        <small style="color: #087cf5; font-size: 11px; margin-left: 4px;">(Lihat Produk)</small>
                                    </a>

                                </strong>

                            </td>


                            <!-- AKSI -->

                            <td>


                                <div class="action-buttons">


                                    <!-- EDIT -->

                                    <a
                                        href="{{ route(
                                            'admin.categories.edit',
                                            $category->cat_id
                                        ) }}"
                                        class="btn-edit"
                                        title="Edit"
                                    >

                                        <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>

                                    </a>



                                    <!-- DELETE -->

                                    <form
                                        action="{{ route(
                                            'admin.categories.destroy',
                                            $category->cat_id
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm(
                                            'Yakin ingin menghapus kategori ini?'
                                        );"
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="btn-delete"
                                            title="Hapus"
                                        >

                                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>

                                        </button>


                                    </form>


                                </div>


                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="4"
                                class="empty"
                            >

                                Belum ada data kategori.

                            </td>

                        </tr>


                    @endforelse


                    </tbody>


                </table>


            </div>



            <!-- FOOTER -->

            <div class="table-info">

                Menampilkan
                <strong>
                    {{ $categories->count() }}
                </strong>
                kategori

            </div>


        </section>


    </main>


</div>

</body>

</html>