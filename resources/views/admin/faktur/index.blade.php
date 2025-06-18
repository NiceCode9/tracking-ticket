@extends('layouts.app')

@section('title', 'Data Distributor')

@section('content')

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <nav aria-label="breadcrumb" class="d-none d-md-inline-block">
                <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent">
                    <li class="breadcrumb-item">
                        <a href="#">
                            <svg class="icon icon-xxs" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Data Faktur</li>
                </ol>
            </nav>
            <h2 class="h4">Manajemen Data Faktur</h2>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('faktur.create') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Tambah Faktur
            </a>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_distributor" class="form-label">Distributor</label>
                            <select name="filter_distributor" id="filter_distributor" class="form-control">
                                <option value="">Semua Distributor</option>
                                @foreach ($distributors as $distributor)
                                    <option value="{{ $distributor->id }}">{{ $distributor->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_status" class="form-label">Status</label>
                            <select name="filter_status" id="filter_status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="upcoming">Akan Jatuh Tempo</option>
                                @foreach (\App\Models\Faktur::$statusLabels as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_invoice_from" class="form-label">Tanggal Faktur Dari</label>
                            <input type="date" class="form-control" id="filter_invoice_from" name="filter_invoice_from">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_invoice_to" class="form-label">Tanggal Faktur Sampai</label>
                            <input type="date" class="form-control" id="filter_invoice_to" name="filter_invoice_to">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_due_from" class="form-label">Tanggal Jatuh Tempo Dari</label>
                            <input type="date" class="form-control" id="filter_due_from" name="filter_due_from">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="filter_due_to" class="form-label">Tanggal Jatuh Tempo Sampai</label>
                            <input type="date" class="form-control" id="filter_due_to" name="filter_due_to">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary" id="btn-filter">Filter</button>
                        @if (request('status') == 'upcoming')
                            <a href="{{ route('faktur.index') }}" class="btn btn-secondary">Reset</a>
                        @else
                            <button type="button" class="btn btn-secondary" id="btn-reset">Reset</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">Daftar Faktur</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0 rounded" id="dist-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nomor Faktur</th>
                            <th>Nama Distributor</th>
                            <th>Tanggal Faktur</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Tanggal Tanda Terima</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const statusParam = urlParams.get('status');
            const dueFromParam = urlParams.get('due_from');
            const dueToParam = urlParams.get('due_to');

            let table = $('#dist-table').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    url: "{{ route('faktur.index') }}",
                    data: function(d) {
                        d.distributor_id = $('#filter_distributor').val();
                        d.status = $('#filter_status').val();
                        d.invoice_from = $('#filter_invoice_from').val();
                        d.invoice_to = $('#filter_invoice_to').val();
                        d.due_from = $('#filter_due_from').val();
                        d.due_to = $('#filter_due_to').val();

                        // Apply filter from URL if exists
                        if (statusParam === 'upcoming') {
                            d.status = 'upcoming';
                            d.due_from = dueFromParam;
                            d.due_to = dueToParam;
                        }
                    }
                },
                order: [
                    [3, 'asc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'no_faktur',
                        nama: 'no_faktur',
                    },
                    {
                        data: 'distributor.nama',
                        nama: 'distributor.nama',
                    },
                    {
                        data: 'tgl_faktur',
                        nama: 'tgl_faktur',
                    },
                    {
                        data: 'tgl_jatuh_tempo',
                        nama: 'tgl_jatuh_tempo',
                    },
                    {
                        data: 'tgl_tanda_terima',
                        nama: 'tgl_tanda_terima',
                    },
                    {
                        data: 'status',
                        nama: 'status',
                    },
                    {
                        data: 'action',
                        nama: 'action',
                        orderable: false,
                        searchable: false,
                    },
                ]
            });

            if (statusParam === 'upcoming') {
                $('#filter_status').val('upcoming');
                $('#filter_due_from').val(dueFromParam);
                $('#filter_due_to').val(dueToParam);
            }

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/faktur/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses',
                                    text: response.message
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.message
                                });
                            }
                        });
                    }
                });
            });

            // Handle filter button click
            $('#btn-filter').click(function() {
                table.draw();
            });

            $('#btn-reset').click(function() {
                $('#filter_distributor').val('');
                $('#filter_status').val('');
                $('#filter_invoice_from').val('');
                $('#filter_invoice_to').val('');
                $('#filter_due_from').val('');
                $('#filter_due_to').val('');
                table.draw();
            });
        });
    </script>
@endpush
