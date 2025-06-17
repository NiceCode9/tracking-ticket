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
                    <li class="breadcrumb-item active" aria-current="page">Data Distributor</li>
                </ol>
            </nav>
            <h2 class="h4">Manajemen Data Distributor</h2>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-sm btn-gray-800 d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#createDistModal">
                <svg class="icon icon-xs me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Tambah Distributor
            </button>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">Daftar Mata Pelajaran</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-centered table-nowrap mb-0 rounded" id="dist-table">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Npwp</th>
                            <th>Nama Distributor</th>
                            <th>Whatsapp</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Create/Edit -->
    <div class="modal fade" id="createDistModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalTitle">Tambah Distributor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="distForm">
                    @csrf
                    <input type="hidden" name="id" id="dist_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" id="npwp" name="npwp" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Distributor</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telp" class="form-label">Whatsapp</label>
                            <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link text-gray-600 ms-auto"
                            data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#dist-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('distributor.index') }}",
                order: [
                    [2, 'asc']
                ], // Order by nama column by default
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'npwp',
                        name: 'npwp'
                    },
                    {
                        data: 'nama',
                        name: 'nama',
                    },
                    {
                        data: 'no_telp',
                        name: 'no_telp',
                    },
                    {
                        data: 'alamat',
                        name: 'alamat',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ]
            });

            $('#distForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#dist_id').val();
                let url = id ? `/distributor/${id}` : '/distributor';
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createDistModal').modal('hide');
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
            });

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $('#modalTitle').text('Edit Distributor');

                $.get(`/distributor/${id}/edit`, function(data) {
                    $('#dist_id').val(data.id);
                    $('#npwp').val(data.npwp);
                    $('#nama').val(data.nama);
                    $('#no_telp').val(data.no_telp);
                    $('#alamat').val(data.alamat);
                    $('#createDistModal').modal('show');
                });
            });

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
                            url: `/distributor/${id}`,
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

            // Reset form when modal is closed
            $('#createDistModal').on('hidden.bs.modal', function() {
                $('#distForm')[0].reset();
                $('#dist_id').val('');
                $('#modalTitle').text('Tambah Distributor');
            });
        });
    </script>
@endpush
