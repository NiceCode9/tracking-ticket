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
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('faktur.index') }}">Data Faktur</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Buat Faktur</li>
                </ol>
            </nav>
            <h2 class="h4">{{ isset($faktur) ? 'Edit Faktur' : 'Form Tambah Faktur' }}</h2>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('faktur.index') }}" class="btn btn-sm btn-gray-800 d-inline-flex align-items-center">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">Form Faktur</h5>
        </div>
        <div class="card-body">
            <form action="{{ isset($faktur) ? route('faktur.update', $faktur->id) : route('faktur.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @if (isset($faktur))
                    @method('PUT')
                @endif
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="distributor_id" class="form-label">Distributor</label>
                            <select name="distributor_id" id="distributor_id"
                                class="form-control @error('distributor_id') is-invalid @enderror" required>
                                <option value="">--Pilih Distributor--</option>
                                @foreach ($distributors as $distributor)
                                    <option value="{{ $distributor->id }}"
                                        {{ isset($faktur) && $faktur->distributor_id == $distributor->id ? 'selected' : '' }}>
                                        {{ $distributor->nama }} - {{ $distributor->alamat }}
                                    </option>
                                @endforeach
                            </select>
                            @error('distributor_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="no_faktur" class="form-label">Nomor Faktur</label>
                            <input type="text" class="form-control @error('no_faktur') is-invalid @enderror"
                                name="no_faktur" id="no_faktur"
                                value="{{ isset($faktur) ? $faktur->no_faktur : old('no_faktur') }}" required>
                            @error('no_faktur')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="number" class="form-control @error('nominal') is-invalid @enderror" name="nominal"
                                id="nominal" value="{{ isset($faktur) ? $faktur->nominal : old('nominal') }}" required>
                            @error('nominal')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="0"
                                    {{ (isset($faktur) && $faktur->status == '0') || old('status') == '0' ? 'selected' : '' }}>
                                    Belum Terjadwal</option>
                                <option value="1"
                                    {{ (isset($faktur) && $faktur->status == '1') || old('status') == '1' ? 'selected' : '' }}>
                                    Terjadwal</option>
                                <option value="2"
                                    {{ (isset($faktur) && $faktur->status == '2') || old('status') == '2' ? 'selected' : '' }}>
                                    Jadwal Ulang</option>
                                <option value="3"
                                    {{ (isset($faktur) && $faktur->status == '3') || old('status') == '3' ? 'selected' : '' }}>
                                    Terbayar</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tgl_faktur" class="form-label">Tanggal Faktur</label>
                            <input type="date" class="form-control @error('tgl_faktur') is-invalid @enderror"
                                name="tgl_faktur" id="tgl_faktur"
                                value="{{ isset($faktur) ? $faktur->tgl_faktur->format('Y-m-d') : old('tgl_faktur') }}"
                                required>
                            @error('tgl_faktur')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tgl_jatuh_tempo" class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="date" class="form-control @error('tgl_jatuh_tempo') is-invalid @enderror"
                                name="tgl_jatuh_tempo" id="tgl_jatuh_tempo"
                                value="{{ isset($faktur) ? $faktur->tgl_jatuh_tempo->format('Y-m-d') : old('tgl_jatuh_tempo') }}"
                                required>
                            @error('tgl_jatuh_tempo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tgl_tanda_terima" class="form-label">Tanggal Tanda Terima</label>
                            <input type="date" class="form-control @error('tgl_tanda_terima') is-invalid @enderror"
                                name="tgl_tanda_terima" id="tgl_tanda_terima"
                                value="{{ isset($faktur) ? $faktur->tgl_tanda_terima->format('Y-m-d') : old('tgl_tanda_terima') }}"
                                required>
                            @error('tgl_tanda_terima')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-bukti"
                            style="display: {{ (isset($faktur) && $faktur->status == '3') || old('status') == '3' ? 'block' : 'none' }};">
                            <label for="bukti_path" class="form-label">Bukti Bayar</label>
                            @if (isset($faktur) && $faktur->bukti_path)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/bukti/' . $faktur->bukti_path) }}" alt="Bukti Pembayaran"
                                        class="img-thumbnail" style="max-height: 200px">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('bukti_path') is-invalid @enderror"
                                name="bukti_path">
                            @error('bukti_path')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#status').change(function(e) {
                e.preventDefault();
                let val = $(this).val();
                if (val == 3) {
                    $('.form-bukti').show();
                } else {
                    $('.form-bukti').hide();
                }
            });
        });
    </script>
@endpush
