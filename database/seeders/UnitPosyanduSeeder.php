<?php

namespace Database\Seeders;

use App\Models\UnitPosyandu;
use App\Models\Kecamatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitPosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kecamatan_id' => 'KEC001',
                'name' => 'Posyandu Ngemplak',
            ],
            [
                'kecamatan_id' => 'KEC002',
                'name' => 'Posyandu Gamping',
            ],
            [
                'kecamatan_id' => 'KEC003',
                'name' => 'Posyandu Minggir',
            ],
            [
                'kecamatan_id' => 'KEC004',
                'name' => 'Posyandu Kalasan',
            ],
            [
                'kecamatan_id' => 'KEC005',
                'name' => 'Posyandu Berbah',
            ],
            [
                'id' => 'DINKES',
                'name' => 'Dinas Kesehatan'
            ]
            ];

            $unitCount = 1;
            foreach ($data as $unitPosyandu) {
                if (!isset($unitPosyandu['id'])) {
                    $unitPosyandu['id'] = 'UNIT' . str_pad($unitCount, 3, '0', STR_PAD_LEFT);
                    $unitCount++;
                }

                UnitPosyandu::create($unitPosyandu);
            }
    }
}
