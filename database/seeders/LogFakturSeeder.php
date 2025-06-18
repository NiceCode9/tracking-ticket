<?php

namespace Database\Seeders;

use App\Models\Faktur;
use App\Models\LogFaktur;
use Illuminate\Database\Seeder;

class LogFakturSeeder extends Seeder
{
    public function run()
    {
        $fakturs = Faktur::all();

        foreach ($fakturs as $faktur) {
            // Log untuk status awal
            LogFaktur::create([
                'faktur_id' => $faktur->id,
                'status' => $faktur->status,
                'keterangan' => 'Faktur dibuat dengan status awal',
                'created_at' => $faktur->created_at
            ]);

            // Jika faktur sudah diterima, tambahkan log penerimaan
            if ($faktur->tgl_tanda_terima) {
                LogFaktur::create([
                    'faktur_id' => $faktur->id,
                    'status' => $faktur->status,
                    'keterangan' => 'Faktur diterima dan diverifikasi',
                    'created_at' => $faktur->tgl_tanda_terima
                ]);
            }

            // Jika status bukan belum terjadwal, tambahkan log perubahan status
            if ($faktur->status != Faktur::STATUS_BELUM_TERJADWAL) {
                LogFaktur::create([
                    'faktur_id' => $faktur->id,
                    'status' => $faktur->status,
                    'keterangan' => 'Status faktur diperbarui',
                    'created_at' => now()->subDays(rand(1, 5))
                ]);
            }
        }
    }
}
