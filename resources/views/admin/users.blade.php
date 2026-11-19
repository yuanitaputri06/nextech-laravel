<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manajemen User - NexTech</title>

    <link rel="stylesheet"
          href="{{ asset('assets/css/admin.css') }}">

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


        <!-- HEADER -->

        <header class="admin-header">


            <div>

                <h1>
                    Manajemen User
                </h1>

                <p>
                    Kelola data user NexTech
                </p>

            </div>



            <!-- PROFILE -->

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
             DATA USER
             ================================================= -->

        <div class="dashboard-card">


            <!-- CARD HEADER -->

            <div class="card-header">

                <div>

                    <h2>
                        Data User
                    </h2>

                    <p>
                        Daftar user yang terdaftar di NexTech
                    </p>

                </div>


                <a
                    href="{{ route('admin.users.create') }}"
                    class="btn-primary"
                    style="display: inline-flex; align-items: center; gap: 6px;"
                >
                    <i data-lucide="plus" style="width: 15px; height: 15px;"></i>
                    <span>Tambah User</span>
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



            <!-- =================================================
                 TABLE
                 ================================================= -->

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
                                Nama
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                No. HP
                            </th>

                            <th>
                                Aksi
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                    @forelse($users as $index => $user)


                        <tr>


                            <!-- NOMOR -->

                            <td>

                                {{ $index + 1 }}

                            </td>



                            <!-- ID -->

                            <td>

                                <span class="product-id">

                                    #{{ $user->user_id }}

                                </span>

                            </td>



                            <!-- NAMA -->

                            <td>

                                <strong class="product-name">

                                    {{ $user->first_name }}
                                    {{ $user->last_name }}

                                </strong>

                            </td>



                            <!-- EMAIL -->

                            <td>

                                {{ $user->email }}

                            </td>



                            <!-- MOBILE -->

                            <td>

                                {{ $user->mobile }}

                            </td>



                            <!-- AKSI -->

                            <td>

                                <div class="action-buttons">

                                    <!-- EDIT -->
                                    <a
                                        href="{{ route('admin.users.edit', $user->user_id) }}"
                                        class="btn-edit"
                                        title="Edit User"
                                    >
                                        <i data-lucide="pencil" style="width: 14px; height: 14px;"></i>
                                    </a>

                                    <!-- DELETE -->
                                    <form
                                        action="{{ route('admin.users.destroy', $user->user_id) }}"
                                        method="POST"
                                        style="display: inline;"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn-delete"
                                            title="Hapus User"
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
                                colspan="6"
                                class="empty"
                            >

                                <div class="empty-icon">
                                    <i data-lucide="users" style="width: 36px; height: 36px; color: #94a3b8;"></i>
                                </div>

                                <h3>
                                    Belum ada user
                                </h3>

                                <p>
                                    Data user belum tersedia.
                                </p>

                            </td>

                        </tr>


                    @endforelse


                    </tbody>


                </table>

            </div>



            <!-- TABLE INFO -->

            <div class="table-info">

                Menampilkan
                {{ $users->count() }}
                data user

            </div>


        </div>


    </main>


</div>

</body>

</html>