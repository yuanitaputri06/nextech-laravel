<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Laporan Penjualan - NexTech</title>

    <link
        rel="stylesheet"
        href="{{ asset('assets/css/admin.css') }}"
    >

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
                    Laporan Penjualan
                </h1>

                <p>
                    Rekap transaksi dan penjualan NexTech
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
             RINGKASAN LAPORAN
             ================================================= -->

        <section
            class="stats-grid"
            style="margin-bottom:25px;"
        >


            <!-- TOTAL TRANSAKSI -->

            <div class="stat-card">

                <div class="stat-icon">
                    <i data-lucide="shopping-bag"></i>
                </div>

                <div>

                    <span class="stat-label">
                        Total Transaksi
                    </span>

                    <strong class="stat-value">
                        {{ $totalTransactions }}
                    </strong>

                </div>

            </div>



            <!-- PRODUK TERJUAL -->

            <div class="stat-card">

                <div class="stat-icon">
                    <i data-lucide="package"></i>
                </div>

                <div>

                    <span class="stat-label">
                        Produk Terjual
                    </span>

                    <strong class="stat-value">
                        {{ $totalSold }}
                    </strong>

                </div>

            </div>



            <!-- TOTAL PENJUALAN -->

            <div class="stat-card">

                <div class="stat-icon">
                    <i data-lucide="wallet"></i>
                </div>

                <div>

                    <span class="stat-label">
                        Total Penjualan
                    </span>

                    <strong class="stat-value">

                        Rp
                        {{ number_format(
                            $totalSales,
                            0,
                            ',',
                            '.'
                        ) }}

                    </strong>

                </div>

            </div>



            <!-- SELESAI -->

            <div class="stat-card">

                <div class="stat-icon">
                    
                </div>

                <div>

                    <span class="stat-label">
                        Pesanan Selesai
                    </span>

                    <strong class="stat-value">
                        {{ $completedOrders }}
                    </strong>

                </div>

            </div>



            <!-- PENDING -->

            <div class="stat-card">

                <div class="stat-icon">
                    ⏳
                </div>

                <div>

                    <span class="stat-label">
                        Pesanan Pending
                    </span>

                    <strong class="stat-value">
                        {{ $pendingOrders }}
                    </strong>

                </div>

            </div>


        </section>



        <!-- =================================================
             DATA LAPORAN
             ================================================= -->

        <section class="dashboard-card">


            <!-- CARD HEADER -->

            <div class="card-header">

                <div>

                    <h2>
                        Data Laporan
                    </h2>

                    <p>
                        Rekap transaksi yang tersimpan di database.
                    </p>

                </div>

            </div>



            <!-- =================================================
                 TABLE
                 ================================================= -->

            <div class="table-wrapper">

                <table>


                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                Transaksi
                            </th>

                            <th>
                                User
                            </th>

                            <th>
                                Produk
                            </th>

                            <th>
                                Qty
                            </th>

                            <th>
                                Harga
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>



                    <tbody>


                    @forelse($reports as $index => $report)


                        <tr>


                            <!-- NOMOR -->

                            <td>

                                {{ $index + 1 }}

                            </td>



                            <!-- TRANSAKSI -->

                            <td>

                                <strong>

                                    {{ $report->trx_id }}

                                </strong>

                            </td>



                            <!-- USER -->

                            <td>

                                @if(
                                    $report->first_name ||
                                    $report->last_name
                                )

                                    {{ $report->first_name }}
                                    {{ $report->last_name }}

                                @else

                                    User #{{ $report->user_id }}

                                @endif

                            </td>



                            <!-- PRODUK -->

                            <td>

                                <strong class="product-name">

                                    {{ $report->product_title ?? 'Produk tidak ditemukan' }}

                                </strong>

                            </td>



                            <!-- QTY -->

                            <td>

                                {{ $report->qty }}

                            </td>



                            <!-- HARGA -->

                            <td class="price">

                                Rp
                                {{ number_format(
                                    $report->product_price ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>



                            <!-- TOTAL -->

                            <td class="price">

                                Rp
                                {{ number_format(
                                    ($report->qty ?? 0) *
                                    ($report->product_price ?? 0),
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>



                            <!-- STATUS -->

                            <td>

                                @if(
                                    strtolower($report->p_status)
                                    === 'completed'
                                )

                                    <span
                                        class="status-badge"
                                        style="
                                            color:#15803d;
                                            background:#dcfce7;
                                            padding:5px 10px;
                                            border-radius:6px;
                                            font-size:12px;
                                            font-weight:600;
                                        "
                                    >

                                        Completed

                                    </span>

                                @elseif(
                                    strtolower($report->p_status)
                                    === 'pending'
                                )

                                    <span
                                        class="status-badge"
                                        style="
                                            color:#b45309;
                                            background:#fef3c7;
                                            padding:5px 10px;
                                            border-radius:6px;
                                            font-size:12px;
                                            font-weight:600;
                                        "
                                    >

                                        Pending

                                    </span>

                                @else

                                    <span
                                        class="status-badge"
                                        style="
                                            padding:5px 10px;
                                            border-radius:6px;
                                            font-size:12px;
                                            font-weight:600;
                                        "
                                    >

                                        {{ $report->p_status }}

                                    </span>

                                @endif

                            </td>


                        </tr>


                    @empty


                        <tr>

                            <td
                                colspan="8"
                                class="empty"
                            >

                                <div class="empty-icon">
                                    <i data-lucide="chart-column" style="width: 36px; height: 36px; color: #94a3b8;"></i>
                                </div>

                                <h3>
                                    Belum ada laporan
                                </h3>

                                <p>
                                    Data transaksi belum tersedia.
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
                {{ $reports->count() }}
                data transaksi

            </div>


        </section>


    </main>


</div>

</body>

</html>