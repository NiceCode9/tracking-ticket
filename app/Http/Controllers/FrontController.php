<?php

namespace App\Http\Controllers;

use App\Models\Faktur;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index()
    {
        return view('landing-page-new');
    }

    public function search(Request $request)
    {
        $noFaktur = $request->query('no_faktur');

        $faktur = Faktur::with(['distributor', 'logs' => function ($query) {
            $query->latest()->take(5); // Ambil 5 log terbaru
        }])
            ->where('no_faktur', $noFaktur)
            ->first();

        if (!$faktur) {
            return response()->json([
                'success' => false,
                'message' => 'Faktur tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'distributor' => [
                    'name' => $faktur->distributor->nama,
                    'address' => $faktur->distributor->alamat,
                    'whatsapp' => $faktur->distributor->no_telp,
                ],
                'faktur' => [
                    'no_faktur' => $faktur->no_faktur,
                    'tgl_faktur' => $faktur->tgl_faktur->format('Y-m-d'),
                    'tgl_jatuh_tempo' => $faktur->tgl_jatuh_tempo->format('Y-m-d'),
                    'tgl_tanda_terima' => $faktur->tgl_tanda_terima?->format('Y-m-d'),
                    'nominal' => $faktur->nominal,
                    'status' => $faktur->status,
                    'logs' => $faktur->logs->map(function ($log) {
                        return [
                            'status' => $log->status,
                            'status_label' => $log->status_label,
                            'keterangan' => $log->keterangan,
                            'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                            'user' => $log->user?->name
                        ];
                    })
                ]
            ]
        ]);
    }
}
