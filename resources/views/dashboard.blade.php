@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .icon-shape {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            vertical-align: middle;
            width: 3rem;
            height: 3rem;
        }

        .icon-shape i {
            font-size: 1.25rem;
        }

        .icon-shape-primary {
            background-color: #2C7BE5;
            color: white;
        }

        .icon-shape-secondary {
            background-color: #6E84A3;
            color: white;
        }

        .icon-shape-tertiary {
            background-color: #F6C343;
            color: white;
        }

        .icon-shape-success {
            background-color: #00D97E;
            color: white;
        }
    </style>
@endpush

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center py-4">
        <div class="d-block mb-4 mb-md-0">
            <h2 class="h4">Dashboard</h2>
        </div>
        <div class="btn-toolbar mb-2 mb-md-0">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center">
                <div class="me-2">
                    <select name="month" class="form-select form-select-sm">
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ $selectedMonth == $key ? 'selected' : '' }}>
                                {{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="me-2">
                    <select name="year" class="form-select form-select-sm">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                {{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
    </div>

    @if ($upcomingDueInvoices->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Peringatan!</strong> Ada {{ $upcomingDueInvoices->count() }} faktur yang akan jatuh tempo dalam 7 hari
            ke depan dan belum berstatus terbayar.
            <a href="{{ route('faktur.index') }}?status=upcoming&due_from={{ now()->format('Y-m-d') }}&due_to={{ now()->addDays(7)->format('Y-m-d') }}"
                class="alert-link">Lihat detail</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- @if ($upcomingDueInvoices->count() > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Peringatan!</strong> Ada {{ $upcomingDueInvoices->count() }} faktur yang akan jatuh tempo bulan ini dan
            belum berstatus terbayar.
            <a href="{{ route('faktur.index') }}?status=upcoming&due_from={{ now()->startOfMonth()->format('Y-m-d') }}&due_to={{ now()->endOfMonth()->format('Y-m-d') }}"
                class="alert-link">Lihat detail</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif --}}

    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card border-0 shadow">
                <div class="card-body">
                    <div class="row d-block d-xl-flex align-items-center">
                        <div
                            class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-primary rounded me-4 me-sm-0">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h5">Total Faktur</h2>
                                <h3 class="fw-extrabold mb-1">{{ number_format($invoiceStats['total']) }}</h3>
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
                        <div
                            class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-secondary rounded me-4 me-sm-0">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h5">Belum Terjadwal</h2>
                                <h3 class="fw-extrabold mb-1">{{ number_format($invoiceStats['belum_terjadwal']) }}</h3>
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
                        <div
                            class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-tertiary rounded me-4 me-sm-0">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h5">Terjadwal</h2>
                                <h3 class="fw-extrabold mb-1">{{ number_format($invoiceStats['terjadwal']) }}</h3>
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
                        <div
                            class="col-12 col-xl-5 text-xl-center mb-3 mb-xl-0 d-flex align-items-center justify-content-xl-center">
                            <div class="icon-shape icon-shape-success rounded me-4 me-sm-0">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="col-12 col-xl-7 px-xl-0">
                            <div class="d-none d-sm-block">
                                <h2 class="h5">Terbayar</h2>
                                <h3 class="fw-extrabold mb-1">{{ number_format($invoiceStats['terbayar']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-header">
            <h5 class="mb-0">Faktur Akan Jatuh Tempo</h5>
        </div>
        <div class="card-body">
            @if ($upcomingDueInvoices->count() > 0)
                <div class="table-responsive">
                    <table class="table table-centered table-nowrap mb-0 rounded">
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
                        <tbody>
                            @foreach ($upcomingDueInvoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->no_faktur }}</td>
                                    <td>{{ $invoice->distributor->nama }}</td>
                                    <td>{{ $invoice->tgl_jatuh_tempo->format('d F Y') }}</td>
                                    <td><span
                                            class="badge bg-{{ $invoice->status == \App\Models\Faktur::STATUS_TERJADWAL ? 'primary' : 'warning' }}">{{ $invoice->status_label }}</span>
                                    </td>
                                    <td>Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('faktur.edit', $invoice->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada faktur yang akan jatuh tempo dalam 7 hari ke depan.
                </div>
            @endif
        </div>
    </div>
@endsection
