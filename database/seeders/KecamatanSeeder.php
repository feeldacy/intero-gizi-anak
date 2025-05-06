<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kecamatan')->insert([
            ['id' => 'KC-001', 'name' => 'Berbah'],
            ['id' => 'KC-002', 'name' => 'Cangrkingan'],
            ['id' => 'KC-003', 'name' => 'Depok'],
            ['id' => 'KC-004', 'name' => 'Gamping'],
            ['id' => 'KC-005', 'name' => 'Godean'],
            ['id' => 'KC-006', 'name' => 'Kalasan'],
            ['id' => 'KC-007', 'name' => 'Minggir'],
            ['id' => 'KC-008', 'name' => 'Mlati'],
            ['id' => 'KC-009', 'name' => 'Moyudan'],
            ['id' => 'KC-010', 'name' => 'Ngaglik'],
            ['id' => 'KC-011', 'name' => 'Ngemplak'],
            ['id' => 'KC-012', 'name' => 'Pakem'],
            ['id' => 'KC-013', 'name' => 'Prambanan'],
            ['id' => 'KC-014', 'name' => 'Sayegan'],
            ['id' => 'KC-015', 'name' => 'Sleman'],
            ['id' => 'KC-016', 'name' => 'Tempel'],
            ['id' => 'KC-017', 'name' => 'Turi'],
        ]);
    }
}
