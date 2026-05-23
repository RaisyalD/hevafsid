<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catMap = Category::pluck('id', 'name');

        $products = [
            ['category' => 'Hijab Segi Empat', 'sku' => 'PSH-HIJ-001', 'name' => 'Hijab Voal Premium Anti Kusut',  'sell' => 65000,  'cost' => 38000,  'min' => 10],
            ['category' => 'Hijab Pashmina',   'sku' => 'PSH-HIJ-002', 'name' => 'Pashmina Crinkle Airflow Lebar', 'sell' => 70000,  'cost' => 40000,  'min' => 10],
            ['category' => 'Hijab Instant',    'sku' => 'PSH-HIJ-003', 'name' => 'Bergo Jersey Kaos Instan',       'sell' => 55000,  'cost' => 28000,  'min' => 12],
            ['category' => 'Ciput & Inner',    'sku' => 'PSH-CIP-001', 'name' => 'Ciput Rajut Antem Anti Geser',  'sell' => 25000,  'cost' => 12000,  'min' => 20],
            ['category' => 'Gamis & Abaya',    'sku' => 'PSH-GAM-001', 'name' => 'Gamis Ceruti Premium Motif',    'sell' => 295000, 'cost' => 175000, 'min' => 5],
            ['category' => 'Mukena',           'sku' => 'PSH-MUK-001', 'name' => 'Mukena Katun Jepang Set Tas',   'sell' => 185000, 'cost' => 110000, 'min' => 5],
        ];

        foreach ($products as $p) {
            $catId = $catMap[$p['category']] ?? null;
            if (! $catId) continue;

            Product::firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'category_id'        => $catId,
                    'sku'                => $p['sku'],
                    'barcode'            => strtoupper(preg_replace('/[^A-Z0-9]/', '', $p['sku'])),
                    'name'               => $p['name'],
                    'unit'               => 'pcs',
                    'sell_price'         => $p['sell'],
                    'default_cost_price' => $p['cost'],
                    'stock_total'        => 0,
                    'min_stock'          => $p['min'],
                    'is_active'          => true,
                ]
            );
        }
    }
}