<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manajemen Brand - NexTech</title>

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
                    Manajemen Brand
                </h1>

                <p>
                    Kelola brand produk NexTech
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



        <!-- CONTENT -->

        <div class="dashboard-card">


            <!-- HEADER CARD -->

            <div class="card-header">

                <div>

                    <h2>
                        Data Brand
                    </h2>

                    <p>
                        Daftar brand yang tersedia
                    </p>

                </div>


                <a
                    href="{{ route('admin.brands.create') }}"
                    class="btn-primary"
                    style="display: inline-flex; align-items: center; gap: 6px;"
                >
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Tambah Brand</span>
                </a>

            </div>



            <!-- SUCCESS -->

            @if(session('success'))

                <div class="alert alert-success">

                    {{ session('success') }}

                </div>

            @endif



            <!-- ERROR -->

            @if(session('error'))

                <div class="alert alert-error">

                    {{ session('error') }}

                </div>

            @endif



            <!-- TABLE -->

            <div class="table-wrapper">

                <table class="product-table">

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                ID
                            </th>

                            <th>
                                Nama Brand
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($brands as $index => $brand)

                        <tr>


                            <td>
                                {{ $index + 1 }}
                            </td>


                            <td>

                                <span class="product-id">
                                    #{{ $brand->brand_id }}
                                </span>

                            </td>


                            <td>

                                <strong class="product-name">

                                    <a href="{{ route('admin.products', ['brand' => $brand->brand_id]) }}" style="color: #102a56; text-decoration: none;" title="Lihat produk dengan brand ini">
                                        {{ $brand->brand_title }}
                                        <small style="color: #087cf5; font-size: 11px; margin-left: 4px;">(Lihat Produk)</small>
                                    </a>

                                </strong>

                            </td>


                            <td>

                                <div class="action-buttons">


                                    <!-- EDIT -->

                                    <a
                                        href="{{ route(
                                            'admin.brands.edit',
                                            $brand->brand_id
                                        ) }}"
                                        class="btn-edit"
                                        title="Edit Brand"
                                    >

                                        <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>

                                    </a>



                                    <!-- DELETE -->

                                    <form
                                        action="{{ route(
                                            'admin.brands.destroy',
                                            $brand->brand_id
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Yakin ingin menghapus brand ini?'
                                            );
                                        "
                                    >

                                        @csrf

                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="btn-delete"
                                            title="Hapus Brand"
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

                                Belum ada data brand.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            <!-- TABLE INFO -->

            <div class="table-info">

                Menampilkan
                {{ $brands->count() }}
                data brand

            </div>


        </div>


    </main>

</div>

</body>

</html>