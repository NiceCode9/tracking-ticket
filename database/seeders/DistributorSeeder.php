<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'npwp' => '01.234.567.8-123.000',
                'nama' => 'PT Maju Bersama',
                'no_telp' => '021-5551234',
                'alamat' => 'Jl. Raya Utama No. 123, Jakarta Pusat',
            ],
            [
                'npwp' => '02.345.678.9-234.000',
                'nama' => 'CV Sukses Mandiri',
                'no_telp' => '021-5552345',
                'alamat' => 'Jl. Kebon Jeruk No. 45, Jakarta Barat',
            ],
            [
                'npwp' => '03.456.789.0-345.000',
                'nama' => 'PT Makmur Jaya',
                'no_telp' => '021-5553456',
                'alamat' => 'Jl. Gatot Subroto No. 67, Jakarta Selatan',
            ],
            [
                'npwp' => '04.567.890.1-456.000',
                'nama' => 'PT Sentosa Abadi',
                'no_telp' => '021-5554567',
                'alamat' => 'Jl. Sudirman No. 89, Jakarta Pusat',
            ],
            [
                'npwp' => '05.678.901.2-567.000',
                'nama' => 'CV Berkah Sejahtera',
                'no_telp' => '021-5555678',
                'alamat' => 'Jl. Thamrin No. 12, Jakarta Pusat',
            ],
        ];

        foreach ($distributors as $distributor) {
            Distributor::create($distributor);
        }
    }
}
