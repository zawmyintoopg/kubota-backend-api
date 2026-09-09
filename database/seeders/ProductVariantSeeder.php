<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // Example: variant for first 5 products
        $variants = [
            [
                'product_id' => 1, // Cotton T-Shirt
                'unit_id' => 1,    // Pcs
                'last_purchase_price' => 5.00,
                'avg_purchase_price' => 5.00,
                'last_purchase_date' => now()->subDays(10),
                'sell_price' => 7.00,
                'status' => 'active',
            ],
            [
                'product_id' => 1, // Cotton T-Shirt
                'unit_id' => 2,    // Box (6 Pcs)
                'last_purchase_price' => 28.00, // 6 Pcs * 5
                'avg_purchase_price' => 28.00,
                'last_purchase_date' => now()->subDays(10),
                'sell_price' => 40.00, 
                'status' => 'active',
            ],
            [
                'product_id' => 2, // Denim Jeans
                'unit_id' => 1, // Pcs
                'last_purchase_price' => 15.00,
                'avg_purchase_price' => 15.00,
                'last_purchase_date' => now()->subDays(8),
                'sell_price' => 22.00,
                'status' => 'active',
            ],
            [
                'product_id' => 2, // Denim Jeans
                'unit_id' => 3, // Packen (12 Pcs)
                'last_purchase_price' => 180.00, // 12 * 15
                'avg_purchase_price' => 180.00,
                'last_purchase_date' => now()->subDays(8),
                'sell_price' => 264.00, // 12 * 22
                'status' => 'active',
            ],
            [
                'product_id' => 3, // Summer Dress
                'unit_id' => 1, // Pcs
                'last_purchase_price' => 12.00,
                'avg_purchase_price' => 12.00,
                'last_purchase_date' => now()->subDays(7),
                'sell_price' => 18.00,
                'status' => 'active',
            ],
            [
                'product_id' => 3, // Summer Dress
                'unit_id' => 2, // Box (6 Pcs)
                'last_purchase_price' => 70.00, // 6 * 12
                'avg_purchase_price' => 70.00,
                'last_purchase_date' => now()->subDays(7),
                'sell_price' => 108.00, // 6 * 18
                'status' => 'active',
            ],
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->insert(array_merge($variant, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
