<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use App\Models\Faktur;
use App\Models\LogFaktur;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class FakturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     if ($request->ajax()) {
    //         $query = Faktur::with('distributor');

    //         // Filter by distributor
    //         if ($request->has('distributor_id') && $request->distributor_id != '') {
    //             $query->where('distributor_id', $request->distributor_id);
    //         }

    //         // Filter by status
    //         if ($request->has('status') && $request->status != '') {
    //             $query->where('status', $request->status);
    //         }

    //         // Filter by date range
    //         if ($request->has('from_date') && $request->from_date != '') {
    //             $query->whereDate('tgl_faktur', '>=', $request->from_date);
    //         }
    //         if ($request->has('to_date') && $request->to_date != '') {
    //             $query->whereDate('tgl_faktur', '<=', $request->to_date);
    //         }

    //         $data = $query->get();

    //         return DataTables::of($data)
    //             ->addIndexColumn()
    //             ->addColumn('tgl_faktur', function ($row) {
    //                 return Carbon::parse($row->tgl_faktur)->translatedFormat('d F Y');
    //             })
    //             ->addColumn('tgl_jatuh_tempo', function ($row) {
    //                 return Carbon::parse($row->tgl_jatuh_tempo)->translatedFormat('d F Y');
    //             })
    //             ->addColumn('tgl_tanda_terima', function ($row) {
    //                 return Carbon::parse($row->tgl_tanda_terima)->translatedFormat('d F Y');
    //             })
    //             ->addColumn('status', function ($row) {
    //                 $badgeClass = match ($row->status) {
    //                     Faktur::STATUS_BELUM_TERJADWAL => 'bg-secondary',
    //                     Faktur::STATUS_TERJADWAL => 'bg-primary',
    //                     Faktur::STATUS_JADWAL_ULANG => 'bg-warning',
    //                     Faktur::STATUS_TERBAYAR => 'bg-success',
    //                     default => 'bg-light'
    //                 };

    //                 return sprintf(
    //                     '<span class="badge %s">%s</span>',
    //                     $badgeClass,
    //                     $row->status_label
    //                 );
    //             })
    //             ->addColumn('action', function ($row) {
    //                 $btn = '
    //                     <a href="' . route('faktur.edit', $row->id) . '" class="btn btn-sm btn-warning me-1 btn-edit" data-id="' . $row->id . '">
    //                         <i class="fas fa-edit"></i>
    //                     </a>
    //                     <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
    //                         <i class="fas fa-trash"></i>
    //                     </button>
    //                 ';
    //                 return $btn;
    //             })
    //             ->rawColumns(['status', 'action'])
    //             ->make(true);
    //     }
    //     $distributors = Distributor::orderBy('nama', 'ASC')->get();
    //     return view('admin.faktur.index', compact('distributors'));
    // }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Faktur::with('distributor');

            // Filter by distributor
            if ($request->has('distributor_id') && $request->distributor_id != '') {
                $query->where('distributor_id', $request->distributor_id);
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                if ($request->status == 'upcoming') {
                    // Special filter for upcoming due invoices
                    $query->where('tgl_jatuh_tempo', '<=', Carbon::now()->addDays(7))
                        ->where('tgl_jatuh_tempo', '>=', Carbon::now())
                        ->where('status', '!=', Faktur::STATUS_TERBAYAR);
                } else {
                    $query->where('status', $request->status);
                }
            }

            // Filter by invoice date (tgl_faktur)
            if ($request->has('invoice_from') && $request->invoice_from != '') {
                $query->whereDate('tgl_faktur', '>=', $request->invoice_from);
            }
            if ($request->has('invoice_to') && $request->invoice_to != '') {
                $query->whereDate('tgl_faktur', '<=', $request->invoice_to);
            }

            // Filter by due date (tgl_jatuh_tempo)
            if ($request->has('due_from') && $request->due_from != '') {
                $query->whereDate('tgl_jatuh_tempo', '>=', $request->due_from);
            }
            if ($request->has('due_to') && $request->due_to != '') {
                $query->whereDate('tgl_jatuh_tempo', '<=', $request->due_to);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl_faktur', function ($row) {
                    return Carbon::parse($row->tgl_faktur)->translatedFormat('d F Y');
                })
                ->addColumn('tgl_jatuh_tempo', function ($row) {
                    return Carbon::parse($row->tgl_jatuh_tempo)->translatedFormat('d F Y');
                })
                ->addColumn('tgl_tanda_terima', function ($row) {
                    return Carbon::parse($row->tgl_tanda_terima)->translatedFormat('d F Y');
                })
                ->addColumn('status', function ($row) {
                    $badgeClass = match ($row->status) {
                        Faktur::STATUS_BELUM_TERJADWAL => 'bg-secondary',
                        Faktur::STATUS_TERJADWAL => 'bg-primary',
                        Faktur::STATUS_JADWAL_ULANG => 'bg-warning',
                        Faktur::STATUS_TERBAYAR => 'bg-success',
                        default => 'bg-light'
                    };

                    return sprintf(
                        '<span class="badge %s">%s</span>',
                        $badgeClass,
                        $row->status_label
                    );
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <a href="' . route('faktur.edit', $row->id) . '" class="btn btn-sm btn-warning me-1 btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </a>
                    ';

                    if ($row->bukti_path) {
                        $btn .= '
                            <a href="' . route('faktur.download', $row->id) . '" class="btn btn-sm btn-info me-1" title="Download Bukti Bayar">
                                <i class="fas fa-download"></i>
                            </a>
                        ';
                    }

                    $btn .= '
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';

                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $distributors = Distributor::orderBy('nama', 'ASC')->get();
        return view('admin.faktur.index', compact('distributors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $distributors = Distributor::orderBy('nama', 'DESC')->get();
        return view('admin.faktur.form', compact('distributors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'distributor_id' => 'required|exists:distributors,id',
    //         'no_faktur' => 'required|unique:fakturs,no_faktur',
    //         'tgl_faktur' => 'required|date',
    //         'tgl_jatuh_tempo' => 'required|date',
    //         'tgl_tanda_terima' => 'required|date',
    //         'nominal' => 'required|numeric',
    //         'status' => 'required|in:0,1,2,3',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $data = $request->all();

    //         if ($request->hasFile('bukti_path')) {
    //             $request->validate([
    //                 'bukti_path' => 'required|mimes:png,jpg,jpeg|max:2048',
    //             ]);

    //             $file = $request->file('bukti_path');
    //             $filename = time() . '_' . $file->getClientOriginalName();
    //             $file->storeAs('public/bukti', $filename);
    //             $data['bukti_path'] = $filename;
    //         }

    //         $faktur = Faktur::create($data);

    //         LogFaktur::create([
    //             'faktur_id' => $faktur->id,
    //             'status' => $faktur->status,
    //             'keterangan' => 'Data di Input dengan status awal ' . $faktur->status_label,
    //             'user_id' => auth()->id()
    //         ]);

    //         DB::commit();
    //         return redirect()->route('faktur.index')->with('success', 'Data faktur berhasil ditambahkan');
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         DB::rollBack();
    //         return redirect()->route('faktur.index')->with('error', 'Data faktur gagal ditambahkan');
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'no_faktur' => 'required|unique:fakturs,no_faktur',
            'tgl_faktur' => 'required|date',
            'tgl_jatuh_tempo' => 'required|date',
            'tgl_tanda_terima' => 'required|date',
            'nominal' => 'required|numeric',
            'status' => 'required|in:0,1,2,3',
            'bukti_path' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('bukti_path');

            if ($request->hasFile('bukti_path')) {
                $file = $request->file('bukti_path');
                $filename = 'bukti_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti', $filename, 'public');
                $data['bukti_path'] = $path; // Simpan path relatif ke storage
            }

            $faktur = Faktur::create($data);

            LogFaktur::create([
                'faktur_id' => $faktur->id,
                'status' => $faktur->status,
                'keterangan' => 'Data di Input dengan status awal ' . $faktur->status_label,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('faktur.index')->with('success', 'Data faktur berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            // Hapus file jika ada error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return redirect()->route('faktur.index')->with('error', 'Data faktur gagal ditambahkan: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Faktur $faktur)
    {
        // return view('admin.faktur.show', compact('faktur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faktur $faktur)
    {
        $distributors = Distributor::orderBy('nama', 'DESC')->get();
        return view('admin.faktur.form', compact('faktur', 'distributors'));
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Faktur $faktur)
    // {
    //     $request->validate([
    //         'distributor_id' => 'required|exists:distributors,id',
    //         'no_faktur' => 'required|unique:fakturs,no_faktur,' . $faktur->id,
    //         'tgl_faktur' => 'required|date',
    //         'tgl_jatuh_tempo' => 'required|date',
    //         'tgl_tanda_terima' => 'required|date',
    //         'nominal' => 'required|numeric',
    //         'status' => 'required|in:0,1,2,3',
    //     ]);

    //     $data = $request->all();

    //     if ($request->hasFile('bukti_path')) {
    //         $request->validate([
    //             'bukti_path' => 'required|mimes:png,jpg,jpeg|max:2048',
    //         ]);

    //         // Hapus file lama jika ada
    //         if ($faktur->bukti_path && file_exists(storage_path('app/public/bukti/' . $faktur->bukti_path))) {
    //             unlink(storage_path('app/public/bukti/' . $faktur->bukti_path));
    //         }

    //         $file = $request->file('bukti_path');
    //         $filename = time() . '_' . $file->getClientOriginalName();
    //         $file->storeAs('public/bukti', $filename);
    //         $data['bukti_path'] = $filename;
    //     }

    //     $old_status = $faktur->status;
    //     $faktur->update($data);

    //     LogFaktur::create([
    //         'faktur_id' => $faktur->id,
    //         'status' => $faktur->status,
    //         'keterangan' => 'Status berubah dari ' .
    //             Faktur::$statusLabels[$old_status] . ' menjadi ' .
    //             $faktur->status_label,
    //         'user_id' => auth()->id()
    //     ]);

    //     return redirect()->route('faktur.index')->with('success', 'Data faktur berhasil diperbarui');
    // }

    public function update(Request $request, Faktur $faktur)
    {
        $request->validate([
            'distributor_id' => 'required|exists:distributors,id',
            'no_faktur' => 'required|unique:fakturs,no_faktur,' . $faktur->id,
            'tgl_faktur' => 'required|date',
            'tgl_jatuh_tempo' => 'required|date',
            'tgl_tanda_terima' => 'required|date',
            'nominal' => 'required|numeric',
            'status' => 'required|in:0,1,2,3',
            'bukti_path' => 'nullable|mimes:png,jpg,jpeg,pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except('bukti_path');
            $oldFilePath = $faktur->bukti_path;

            if ($request->hasFile('bukti_path')) {
                // Upload file baru
                $file = $request->file('bukti_path');
                $filename = 'bukti_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('bukti', $filename, 'public');
                $data['bukti_path'] = $path;

                // Hapus file lama jika ada
                if ($oldFilePath) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }

            $old_status = $faktur->status;
            $faktur->update($data);

            LogFaktur::create([
                'faktur_id' => $faktur->id,
                'status' => $faktur->status,
                'keterangan' => 'Status berubah dari ' .
                    Faktur::$statusLabels[$old_status] . ' menjadi ' .
                    $faktur->status_label,
                'user_id' => auth()->id()
            ]);

            DB::commit();
            return redirect()->route('faktur.index')->with('success', 'Data faktur berhasil diperbarui');
        } catch (\Throwable $th) {
            DB::rollBack();
            // Hapus file baru jika ada error
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }
            return redirect()->route('faktur.index')->with('error', 'Data faktur gagal diperbarui: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faktur $faktur)
    {
        DB::beginTransaction();
        try {
            $filePath = $faktur->bukti_path;

            $faktur->delete();

            // Hapus file jika ada
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Faktur berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menghapus faktur: ' . $th->getMessage()]);
        }
    }

    public function download(Faktur $faktur)
    {
        if (!$faktur->bukti_path) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak tersedia.');
        }

        $filePath = storage_path('app/public/' . $faktur->bukti_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }

        return response()->download($filePath);
    }
}
