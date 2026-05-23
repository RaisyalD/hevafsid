<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'role'  => 'super_admin',
                'name'  => 'Admin System',
                'email' => 'admin@pinkstock.id',
                'pass'  => 'password',
            ],
            [
                'role'  => 'owner',
                'name'  => 'Bunda Sari (Owner)',
                'email' => 'owner@pinkstock.id',
                'pass'  => 'password',
            ],
            [
                'role'  => 'admin_gudang',
                'name'  => 'Dita Gudang',
                'email' => 'gudang@pinkstock.id',
                'pass'  => 'password',
            ],
            [
                'role'  => 'admin_keuangan',
                'name'  => 'Rina Keuangan',
                'email' => 'keuangan@pinkstock.id',
                'pass'  => 'password',
            ],
        ];

        foreach ($users as $u) {
            $role = Role::where('name', $u['role'])->first();
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'role_id'  => $role->id,
                    'name'     => $u['name'],
                    'password' => Hash::make($u['pass']),
                    'is_active'=> true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
