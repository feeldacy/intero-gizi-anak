<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'tambah-data-anak']);
        Permission::create(['name' => 'edit-data-anak']);
        Permission::create(['name' => 'hapus-data-anak']);
        Permission::create(['name' => 'lihat-data-anak']);

        Permission::create(['name' => 'tambah-data-gizi-anak']);
        Permission::create(['name' => 'edit-data-gizi-anak']);
        Permission::create(['name' => 'hapus-data-gizi-anak']);
        Permission::create(['name' => 'lihat-data-gizi-anak']);

        Role::create(['name' => 'nutritrackAdmin']);
        Role::create(['name' => 'healthmapAdmin']);

        $roleNutritrackAdmin = Role::findByName('nutritrackAdmin');
        $roleNutritrackAdmin -> givePermissionTo('tambah-data-anak');
        $roleNutritrackAdmin -> givePermissionTo('edit-data-anak');
        $roleNutritrackAdmin -> givePermissionTo('hapus-data-anak');
        $roleNutritrackAdmin -> givePermissionTo('lihat-data-anak');

        $roleNutritrackAdmin -> givePermissionTo('tambah-data-gizi-anak');
        $roleNutritrackAdmin -> givePermissionTo('edit-data-gizi-anak');
        $roleNutritrackAdmin -> givePermissionTo('hapus-data-gizi-anak');
        $roleNutritrackAdmin -> givePermissionTo('lihat-data-gizi-anak');

        $roleHealthmapAdmin = Role::findByName('healthmapAdmin');
        $roleHealthmapAdmin -> givePermissionTo('lihat-data-gizi-anak');
        $roleHealthmapAdmin -> givePermissionTo('lihat-data-anak');
    }
}
