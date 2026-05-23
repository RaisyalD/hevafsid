<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'CAT-001', 'name' => 'Hijab Segi Empat', 'description' => 'Kerudung persegi empat berbagai bahan'],
            ['code' => 'CAT-002', 'name' => 'Hijab Pashmina',   'description' => 'Pashmina panjang dan pendek'],
            ['code' => 'CAT-003', 'name' => 'Hijab Instant',    'description' => 'Hijab langsung pakai / bergo'],
            ['code' => 'CAT-004', 'name' => 'Ciput & Inner',    'description' => 'Ciput rajut dan inner daleman'],
            ['code' => 'CAT-005', 'name' => 'Gamis & Abaya',    'description' => 'Baju gamis dan abaya muslimah'],
            ['code' => 'CAT-006', 'name' => 'Mukena',           'description' => 'Mukena sajadah dan travel'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['code' => $cat['code']], $cat);
        }
    }
}