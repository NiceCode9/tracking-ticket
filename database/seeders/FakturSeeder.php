<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Faktur;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FakturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = Distributor::all();
        $startDate = Carbon::now()->subMonths(2);
        $endDate = Carbon::now();

        for ($i = 0; $i < 50; $i++) {
            // Generate random date between 2 months ago and now
            $tglFaktur = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            )->startOfDay();

            // Set jatuh tempo between 30-60 days after tgl_faktur
            $tglJatuhTempo = (clone $tglFaktur)->addDays(rand(30, 60));

            // Set tanda terima between 1-5 days after tgl_faktur
            $tglTandaTerima = (clone $tglFaktur)->addDays(rand(1, 5));

            // Generate random status with weighted probability
            $status = collect([
                Faktur::STATUS_BELUM_TERJADWAL => '0',  // 30% chance
                Faktur::STATUS_TERJADWAL => '1',        // 40% chance
                Faktur::STATUS_JADWAL_ULANG => '2',     // 20% chance
                Faktur::STATUS_TERBAYAR => '3'          // 10% chance
            ])->shuffle()->flatten()->random();

            Log::info($status);

            $faktur = Faktur::create([
                'distributor_id' => $distributors->random()->id,
                'no_faktur' => 'F' . date('Ym') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'tgl_faktur' => $tglFaktur,
                'tgl_jatuh_tempo' => $tglJatuhTempo,
                'tgl_tanda_terima' => $tglTandaTerima,
                'nominal' => rand(1000000, 50000000), // Random between 1jt - 50jt
                'status' => $status,
            ]);

            // If status is TERBAYAR, add bukti pembayaran
            if ($status === Faktur::STATUS_TERBAYAR) {
                $faktur->update([
                    'bukti_path' => 'sample_bukti_' . rand(1, 5) . '.jpg'
                ]);
            }
        }
    }
}
