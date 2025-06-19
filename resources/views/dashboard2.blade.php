@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .icon-shape i {
            font-size: 1.25rem;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Dashboard <span id="period-label">{{ date('F Y') }}</span></h2>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="d-flex align-items-center">
                <div class="me-2">
                    <select name="month" id="filter-month" class="form-select form-select-sm">
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ $currentMonth == $key ? 'selected' : '' }}>
                                {{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-2">
                    <select name="year" id="filter-year" class="form-select form-select-sm">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btn-filter" class="btn btn-sm btn-primary">Filter</button>
            </div>
        </div>
    </div>

    <div id="alert-container">
        <!-- Alert akan diisi oleh JavaScript -->
    </div>

    <div class="row mb-4" id="stats-container">
        <!-- Statistik akan diisi oleh JavaScript -->
    </div>

    <div class="row mb-4">
        <!-- Widget Piutang -->
        <div class="col-md-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-normal text-muted">Total Piutang</h6>
                            <h3 class="fw-bold" id="total-piutang">Rp 0</h3>
                        </div>
                        <div class="icon-shape bg-primary text-white rounded-circle">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" id="progress-bayar" role="progressbar" style="width: 0%"></div>
                        <div class="progress-bar bg-danger" id="progress-sisa" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-normal text-muted">Sudah Dibayar</h6>
                            <h3 class="fw-bold text-success" id="sudah-dibayar">Rp 0</h3>
                        </div>
                        <div class="icon-shape bg-success text-white rounded-circle">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-normal text-muted">Sisa Piutang</h6>
                            <h3 class="fw-bold text-danger" id="sisa-piutang">Rp 0</h3>
                        </div>
                        <div class="icon-shape bg-danger text-white rounded-circle">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Faktur Akan Jatuh Tempo</h5>
                    <div class="dt-buttons btn-group">
                        <button class="btn btn-sm btn-outline-primary buttons-copy" tabindex="0">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary buttons-excel" tabindex="0">
                            <i class="fas fa-file-excel"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary buttons-pdf" tabindex="0">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body" id="invoices-container">
                    <!-- DataTable akan diinisialisasi oleh JavaScript -->
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow border-0 mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tren Piutang 6 Bulan Terakhir</h5>
                </div>
                <div class="card-body">
                    <canvas id="piutangChart" width="100%"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        $(document).ready(function() {
            // Load data pertama kali
            loadDashboardData($('#filter-month').val(), $('#filter-year').val());

            // Handle filter button click
            $('#btn-filter').click(function() {
                loadDashboardData($('#filter-month').val(), $('#filter-year').val());
            });

            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            }

            function animateValue(elementSelector, start, end, duration) {
                const $element = $(elementSelector); // Pastikan ini adalah selector jQuery
                let startTimestamp = null;

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const value = Math.floor(progress * (end - start) + start);

                    // Gunakan jQuery's text() method
                    $element.text(formatRupiah(value));

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }

            function updateDashboard(response) {
                updatePiutangWidgets(response.piutang);
                // Update period label
                $('#period-label').text(response.period);

                // Update alert
                updateAlert(response.upcomingDueInvoices, $('#filter-month').val(), $('#filter-year').val());

                // Update stats
                updateStats(response.stats);
            }

            function updatePiutangWidgets(piutang) {
                animateValue('#total-piutang', 0, piutang.belum_dibayar, 1000);
                animateValue('#sudah-dibayar', 0, piutang.sudah_dibayar, 1000);
                animateValue('#sisa-piutang', 0, piutang.sisa, 1000);
                // Hitung total piutang (belum dibayar)
                const totalPiutang = piutang.belum_dibayar;

                // Update widget
                $('#total-piutang').text(formatRupiah(totalPiutang));
                $('#sudah-dibayar').text(formatRupiah(piutang.sudah_dibayar));
                $('#sisa-piutang').text(formatRupiah(piutang.sisa));

                // Beri warna berbeda untuk sisa piutang
                if (piutang.sisa > 0) {
                    $('#sisa-piutang').removeClass('text-success').addClass('text-danger');
                } else {
                    $('#sisa-piutang').removeClass('text-danger').addClass('text-success');
                }

                const total = parseInt(piutang.belum_dibayar) + parseInt(piutang.sudah_dibayar);

                if (total > 0) {
                    const persenBayar = (piutang.sudah_dibayar / total) * 100;
                    const persenSisa = (piutang.sisa / total) * 100;

                    $('#progress-bayar').css('width', `${persenBayar}%`);
                    $('#progress-sisa').css('width', `${persenSisa}%`);
                }
            }

            function renderPiutangChart(data) {
                const ctx = document.getElementById('piutangChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                                label: 'Total Piutang',
                                data: data.total,
                                borderColor: '#2C7BE5',
                                backgroundColor: 'rgba(44, 123, 229, 0.1)',
                                tension: 0.3
                            },
                            {
                                label: 'Yang Sudah Dibayar',
                                data: data.paid,
                                borderColor: '#00D97E',
                                backgroundColor: 'rgba(0, 217, 126, 0.1)',
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + context.raw
                                            .toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function loadDashboardData(month, year) {
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: 'GET',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        updateDashboard(response);
                        if (response.chartData) {
                            renderPiutangChart(response.chartData);
                        }
                        initializeDataTable(response.upcomingDueInvoices);
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Gagal memuat data dashboard');
                    }
                });
            }

            function updateAlert(invoices, month, year) {
                const alertContainer = $('#alert-container');
                alertContainer.empty();

                if (invoices.length > 0) {
                    const startDate = new Date(year, month - 1, 1);
                    const endDate = new Date(year, month, 0);

                    const alertHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>Peringatan!</strong> Ada ${invoices.length} faktur yang akan jatuh tempo bulan ini dan belum berstatus terbayar.
                    <a href="{{ route('faktur.index') }}?status=upcoming&due_from=${formatDate(startDate)}&due_to=${formatDate(endDate)}" class="alert-link">Lihat detail</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
                    alertContainer.html(alertHTML);
                }
            }

            function updateStats(stats) {
                const statsContainer = $('#stats-container');

                const statsHTML = `
                    <div class="col-12 col-sm-6 col-xl-3 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="row d-block d-xl-flex align-items-center">
                                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                        <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-7 px-xl-0">
                                        <div class="d-none d-sm-block">
                                            <h2 class="h5">Total Faktur</h2>
                                            <h3 class="fw-extrabold mb-1">${stats.total.toLocaleString()}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="row d-block d-xl-flex align-items-center">
                                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                        <div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-7 px-xl-0">
                                        <div class="d-none d-sm-block">
                                            <h2 class="h5">Belum Terjadwal</h2>
                                            <h3 class="fw-extrabold mb-1">${stats.belum_terjadwal.toLocaleString()}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="row d-block d-xl-flex align-items-center">
                                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                        <div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-7 px-xl-0">
                                        <div class="d-none d-sm-block">
                                            <h2 class="h5">Terjadwal</h2>
                                            <h3 class="fw-extrabold mb-1">${stats.terjadwal.toLocaleString()}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3 mb-4">
                        <div class="card border-0 shadow">
                            <div class="card-body">
                                <div class="row d-block d-xl-flex align-items-center">
                                    <div class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                                        <div class="icon-shape icon-shape-success rounded me-4 me-sm-0">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-7 px-xl-0">
                                        <div class="d-none d-sm-block">
                                            <h2 class="h5">Terbayar</h2>
                                            <h3 class="fw-extrabold mb-1">${stats.terbayar.toLocaleString()}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                statsContainer.html(statsHTML);
            }

            function initializeDataTable(invoices) {
                if ($.fn.DataTable.isDataTable('#invoices-table')) {
                    $('#invoices-table').DataTable().destroy();
                }

                $('#invoices-container').html(`
                    <div class="table-responsive">
                        <table id="invoices-table" class="table table-hover table-sm table-bordered" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>No. Faktur</th>
                                    <th>Distributor</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                `);

                $('#invoices-table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('dashboard.data') }}",
                        data: function(d) {
                            d.month = $('#filter-month').val();
                            d.year = $('#filter-year').val();
                            d.datatable = true;
                        }
                    },
                    columns: [{
                            data: 'no_faktur',
                            name: 'no_faktur'
                        },
                        {
                            data: 'distributor.nama',
                            name: 'distributor.nama'
                        },
                        {
                            data: 'formatted_tgl_jatuh_tempo',
                            name: 'tgl_jatuh_tempo'
                        },
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false
                        },
                        {
                            data: 'formatted_nominal',
                            name: 'nominal'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [2, 'asc']
                    ],
                });
            }

            // Update fungsi loadDashboardData
            function loadDashboardData(month, year) {
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: 'GET',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        updateDashboard(response);
                        if (response.chartData) {
                            renderPiutangChart(response.chartData);
                        }
                        initializeDataTable(response.upcomingDueInvoices);
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Gagal memuat data dashboard');
                    }
                });
            }

            function formatDate(date) {
                return date.toISOString().split('T')[0];
            }

            // function updateInvoicesTable(response) {
            //     const invoicesContainer = $('#invoices-container');
            //     const invoices = response.upcomingDueInvoices;

            //     if (invoices.length > 0) {
            //         let tableHTML = `
        //             <div class="table-responsive">
        //                 <table class="table table-centered table-nowrap mb-0 rounded">
        //                     <thead class="thead-light">
        //                         <tr>
        //                             <th>No. Faktur</th>
        //                             <th>Distributor</th>
        //                             <th>Jatuh Tempo</th>
        //                             <th>Status</th>
        //                             <th>Nominal</th>
        //                             <th>Aksi</th>
        //                         </tr>
        //                     </thead>
        //                     <tbody>
        //         `;

            //         invoices.forEach(invoice => {
            //             tableHTML += `
        //                 <tr>
        //                     <td>${invoice.no_faktur}</td>
        //                     <td>${invoice.distributor.nama}</td>
        //                     <td>${new Date(invoice.tgl_jatuh_tempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</td>
        //                     <td><span class="badge ${response.status_classes[invoice.status]}">${response.status_labels[invoice.status]}</span></td>
        //                     <td>Rp ${invoice.nominal.toLocaleString('id-ID')}</td>
        //                     <td>
        //                         <a href="/faktur/${invoice.id}/edit" class="btn btn-sm btn-primary">
        //                             <i class="fas fa-edit"></i>
        //                         </a>
        //                     </td>
        //                 </tr>
        //             `;
            //         });

            //         tableHTML += `
        //                     </tbody>
        //                 </table>
        //             </div>
        //         `;
            //         invoicesContainer.html(tableHTML);
            //     } else {
            //         invoicesContainer.html(`
        //     <div class="alert alert-info">
        //         Tidak ada faktur yang akan jatuh tempo bulan ini.
        //     </div>
        // `);
            //     }
            // }
        });
    </script>
@endpush
