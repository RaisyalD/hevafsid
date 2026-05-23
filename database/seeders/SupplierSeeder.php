<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'code'           => 'SUP-001',
                'name'           => 'CV. Azzahra Textile',
                'contact_person' => 'Bu Azzahra',
                'phone'          => '0812-3456-7890',
                'email'          => 'azzahra@textile.com',
                'address'        => 'Jl. Tekstil No. 12',
                'city'           => 'Bandung',
            ],
            [
                'code'           => 'SUP-002',
                'name'           => 'PT. Nusantara Fashion',
                'contact_person' => 'Ibu Rani',
                'phone'          => '0811-2233-4455',
                'email'          => 'rani@nusantarafashion.co.id',
                'address'        => 'Jl. Industri Raya No. 88',
                'city'           => 'Solo',
            ],
        ];

        foreach ($suppliers as $sup) {
            Supplier::firstOrCreate(['code' => $sup['code']], $sup);
        }
    }
}