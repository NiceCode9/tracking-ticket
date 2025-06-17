<?php

namespace App\Http\Controllers;

use App\Models\Distributor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Distributor::query();

            return DataTables::of($query)
                ->addIndexColumn()
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
                ->filter(function ($query) use ($request) {
                    if ($request->search['value']) {
                        $query->where(function ($q) use ($request) {
                            $q->where('npwp', 'like', "%{$request->search['value']}%")
                                ->orWhere('nama', 'like', "%{$request->search['value']}%")
                                ->orWhere('no_telp', 'like', "%{$request->search['value']}%")
                                ->orWhere('alamat', 'like', "%{$request->search['value']}%");
                        });
                    }
                })
                ->orderColumn('nama', function ($query, $order) {
                    $query->orderBy('nama', $order);
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.distributor.index');
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
        $request->validate([
            'npwp' => 'required|unique:distributors,npwp',
            'nama' => 'required',
            'no_telp' => 'required|unique:distributors,no_telp'
        ]);

        try {

            Distributor::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Distributor berhasil ditambahkan'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan ' . $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Distributor $distributor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Distributor $distributor)
    {
        return response()->json($distributor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Distributor $distributor)
    {
        $request->validate([
            'npwp' => 'required|unique:distributors,npwp,' . $distributor->id,
            'nama' => 'required',
            'no_telp' => 'required|unique:distributors,no_telp,' .  $distributor->id
        ]);

        try {
            $distributor->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Distributor Berhasil diperbarui',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi Kesalahan Server ' . $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Distributor $distributor)
    {
        try {
            $distributor->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Distributor Berhasi dihapus',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => 'success',
                'message' => 'Terjadi Kesalahan Server ' . $th->getMessage(),
            ], 500);
        }
    }
}
