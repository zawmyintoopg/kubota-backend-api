<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Sample purchase items
        $items = [
            // Purchase 1
            [
                'purchase_id' => 1,
                'product_id' => 1,   // Cotton T-Shirt
                'variant_id' => 1,   // Pcs
                'quantity' => 10,
                'unit_price' => 20000,
                'total_price' => 10 * 20000,
            ],
            [
                'purchase_id' => 1,
                'product_id' => 2,   // Denim Jeans
                'variant_id' => 3,   // Box
                'quantity' => 5,
                'unit_price' => 25000,
                'total_price' => 5 * 25000,
            ],

            // Purchase 2
            [
                'purchase_id' => 2,
                'product_id' => 3,   // Summer Dress
                'variant_id' => 5,
                'quantity' => 8,
                'unit_price' => 22000,
                'total_price' => 8 * 22000,
            ],
            [
                'purchase_id' => 2,
                'product_id' => 4,   // Jacket
                'variant_id' => 7,
                'quantity' => 2,
                'unit_price' => 30000,
                'total_price' => 2 * 30000,
            ],

            // Purchase 3
            [
                'purchase_id' => 3,
                'product_id' => 5,   // Hoodie
                'variant_id' => 9,
                'quantity' => 20,
                'unit_price' => 28000,
                'total_price' => 20 * 28000,
            ],
            [
                'purchase_id' => 3,
                'product_id' => 6,   // Polo Shirt
                'variant_id' => 11,
                'quantity' => 3,
                'unit_price' => 35000,
                'total_price' => 3 * 35000,
            ],
        ];

        foreach ($items as $item) {
            DB::table('purchase_items')->insert(array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
