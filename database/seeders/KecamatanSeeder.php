<?php

namespace Database\Seeders;
use App\Models\Kecamatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $kecamatans = [
            'Ngemplak',
            'Gamping',
            'Minggir',
            'Kalasan',
            'Berbah',
            'Prambanan',
            'Cangkringan',
            'Turi',
            'Seyegan',
            'Sleman'
        ];

        foreach ($kecamatans as $index => $kecamatan) {
            Kecamatan::create([
                'id' => 'KEC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT), // KEC001
                'name' => $kecamatan
            ]);
        }
    }
}
