<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Default bulan dan tahun saat ini
    //     $selectedMonth = $request->get('month', date('m'));
    //     $selectedYear = $request->get('year', date('Y'));

    //     // Query untuk faktur yang akan jatuh tempo
    //     $upcomingDueInvoices = Faktur::where('status', '!=', Faktur::STATUS_TERBAYAR)
    //         ->whereBetween('tgl_jatuh_tempo', [
    //             Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //             Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //         ])
    //         ->with('distributor')
    //         ->orderBy('tgl_jatuh_tempo', 'asc')
    //         ->get();

    //     // Hitung statistik berdasarkan bulan yang dipilih
    //     $invoiceStats = [
    //         'total' => Faktur::whereBetween('tgl_faktur', [
    //             Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //             Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //         ])->count(),
    //         'belum_terjadwal' => Faktur::where('status', Faktur::STATUS_BELUM_TERJADWAL)
    //             ->whereBetween('tgl_faktur', [
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //             ])->count(),
    //         'terjadwal' => Faktur::where('status', Faktur::STATUS_TERJADWAL)
    //             ->whereBetween('tgl_faktur', [
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //             ])->count(),
    //         'jadwal_ulang' => Faktur::where('status', Faktur::STATUS_JADWAL_ULANG)
    //             ->whereBetween('tgl_faktur', [
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //             ])->count(),
    //         'terbayar' => Faktur::where('status', Faktur::STATUS_TERBAYAR)
    //             ->whereBetween('tgl_faktur', [
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth(),
    //                 Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth()
    //             ])->count(),
    //     ];

    //     // Generate pilihan tahun (5 tahun terakhir)
    //     $years = range(date('Y'), date('Y') - 4);
    //     $months = [
    //         '01' => 'Januari',
    //         '02' => 'Februari',
    //         '03' => 'Maret',
    //         '04' => 'April',
    //         '05' => 'Mei',
    //         '06' => 'Juni',
    //         '07' => 'Juli',
    //         '08' => 'Agustus',
    //         '09' => 'September',
    //         '10' => 'Oktober',
    //         '11' => 'November',
    //         '12' => 'Desember'
    //     ];

    //     return view('dashboard', compact(
    //         'upcomingDueInvoices',
    //         'invoiceStats',
    //         'months',
    //         'years',
    //         'selectedMonth',
    //         'selectedYear'
    //     ));
    // }
    public function index()
    {
        // Default bulan dan tahun saat ini
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Generate pilihan tahun (5 tahun terakhir)
        $years = range(date('Y'), date('Y') - 4);
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        return view('dashboard2', compact('months', 'years', 'currentMonth', 'currentYear'));
    }

    // Endpoint baru untuk data dashboard
    public function getDashboardData(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $piutang = [
            'belum_dibayar' => Faktur::where('status', '!=', Faktur::STATUS_TERBAYAR)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])
                ->sum('nominal'),
            'sudah_dibayar' => Faktur::where('status', Faktur::STATUS_TERBAYAR)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])
                ->sum('nominal'),
        ];
        $piutang['sisa'] = $piutang['belum_dibayar'] - $piutang['sudah_dibayar'];

        // Data faktur akan jatuh tempo
        $upcomingDueInvoices = Faktur::where('status', '!=', Faktur::STATUS_TERBAYAR)
            ->whereBetween('tgl_jatuh_tempo', [$startDate, $endDate])
            ->with('distributor')
            ->orderBy('tgl_jatuh_tempo', 'asc')
            ->get();

        // Statistik
        $stats = [
            'total' => Faktur::whereBetween('tgl_faktur', [$startDate, $endDate])->count(),
            'belum_terjadwal' => Faktur::where('status', Faktur::STATUS_BELUM_TERJADWAL)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])->count(),
            'terjadwal' => Faktur::where('status', Faktur::STATUS_TERJADWAL)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])->count(),
            'jadwal_ulang' => Faktur::where('status', Faktur::STATUS_JADWAL_ULANG)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])->count(),
            'terbayar' => Faktur::where('status', Faktur::STATUS_TERBAYAR)
                ->whereBetween('tgl_faktur', [$startDate, $endDate])->count(),
        ];

        return response()->json([
            'upcomingDueInvoices' => $upcomingDueInvoices,
            'stats' => $stats,
            'period' => $startDate->translatedFormat('F Y'),
            'piutang' => $piutang,
            'status_labels' => Faktur::$statusLabels,
            'status_classes' => [
                Faktur::STATUS_BELUM_TERJADWAL => 'bg-secondary',
                Faktur::STATUS_TERJADWAL => 'bg-primary',
                Faktur::STATUS_JADWAL_ULANG => 'bg-warning',
                Faktur::STATUS_TERBAYAR => 'bg-success'
            ]
        ]);
    }
}
