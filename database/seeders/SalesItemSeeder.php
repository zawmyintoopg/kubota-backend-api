<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Sale 1
            [
                'sale_id' => 1,
                'product_id' => 1,   // Cotton T-Shirt
                'variant_id' => 1,   // Pcs
                'quantity' => 5,
                'unit_price' => 25000,
                'total_price' => 5 * 25000,
            ],
            [
                'sale_id' => 1,
                'product_id' => 2,   // Denim Jeans
                'variant_id' => 3,   // Box
                'quantity' => 2,
                'unit_price' => 30000,
                'total_price' => 2 * 30000,
            ],

            // Sale 2
            [
                'sale_id' => 2,
                'product_id' => 3,   // Summer Dress
                'variant_id' => 5,   // Pcs
                'quantity' => 3,
                'unit_price' => 22000,
                'total_price' => 3 * 22000,
            ],
            [
                'sale_id' => 2,
                'product_id' => 4,   // Jacket
                'variant_id' => 7,   // Box
                'quantity' => 1,
                'unit_price' => 35000,
                'total_price' => 35000,
            ],
        ];

        foreach ($items as $item) {
            DB::table('sale_items')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
