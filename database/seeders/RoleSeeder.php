<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin',    'display_name' => 'Super Admin',     'description' => 'Akses penuh ke seluruh sistem'],
            ['name' => 'admin_gudang',   'display_name' => 'Admin Gudang',    'description' => 'Kelola stok, barang masuk/keluar'],
            ['name' => 'admin_keuangan', 'display_name' => 'Admin Keuangan',  'description' => 'Kelola laporan keuangan'],
            ['name' => 'owner',          'display_name' => 'Owner',           'description' => 'Monitoring dashboard dan laporan saja'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
