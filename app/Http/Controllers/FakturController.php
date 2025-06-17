<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FakturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Faktur::with('distributor')->get();

            return DataTables::make($data)
                ->addIndexColumn()
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
                        <button type="button" class="btn btn-sm btn-warning me-1 btn-edit" data-id="' . $row->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="fas fa-trash"></i>
                        </button>
                    ';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Faktur $faktur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faktur $faktur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faktur $faktur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faktur $faktur)
    {
        //
    }
}
