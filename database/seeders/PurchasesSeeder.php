<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchasesSeeder extends Seeder
{
    public function run(): void
    {
        // Sample purchases
        $purchasesData = [
            [
                'purchase_date' => now()->subDays(10),
                'supplier_id' => 1,
                'payment_type_id' => 1,
                'transaction_type_id' => 1,
                'status' => 'completed',
                'items' => [
                    ['product_id'=>1,'variant_id'=>1,'qty'=>10,'unit_price'=>20000], // 200000
                    ['product_id'=>2,'variant_id'=>3,'qty'=>5,'unit_price'=>25000],  // 125000
                ],
            ],
            [
                'purchase_date' => now()->subDays(5),
                'supplier_id' => 2,
                'payment_type_id' => 2,
                'transaction_type_id' => 1,
                'status' => 'draft',
                'items' => [
                    ['product_id'=>3,'variant_id'=>5,'qty'=>8,'unit_price'=>22000],  // 176000
                    ['product_id'=>4,'variant_id'=>7,'qty'=>2,'unit_price'=>30000],  // 60000
                ],
            ],
            [
                'purchase_date' => now()->subDays(2),
                'supplier_id' => 3,
                'payment_type_id' => 3,
                'transaction_type_id' => 1,
                'status' => 'completed',
                'items' => [
                    ['product_id'=>5,'variant_id'=>9,'qty'=>20,'unit_price'=>28000], // 560000
                    ['product_id'=>6,'variant_id'=>11,'qty'=>3,'unit_price'=>35000], // 105000
                ],
            ],
        ];

        foreach ($purchasesData as $purchase) {
            // Calculate totals
            $sub_total = 0;
            foreach ($purchase['items'] as $item) {
                $sub_total += $item['qty'] * $item['unit_price'];
            }

            $discount_amount = 0; // You can add random or fixed discount
            $tax_amount = 0;      // Optional tax
            $grand_total = $sub_total - $discount_amount + $tax_amount;
            $paid_amount = $grand_total; // For simplicity, assume fully paid
            $balance_amount = $grand_total - $paid_amount;

            // Insert purchase
            $purchase_id = DB::table('purchases')->insertGetId([
                'purchase_date' => $purchase['purchase_date'],
                'voucher_no' => 'PUR-'.Str::upper(Str::random(6)),
                'supplier_id' => $purchase['supplier_id'],
                'payment_type_id' => $purchase['payment_type_id'],
                'transaction_type_id' => $purchase['transaction_type_id'],
                'sub_total' => $sub_total,
                'discount_amount' => $discount_amount,
                'tax_amount' => $tax_amount,
                'grand_total' => $grand_total,
                'paid_amount' => $paid_amount,
                'balance_amount' => $balance_amount,
                'status' => $purchase['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert purchase items
            foreach ($purchase['items'] as $item) {
                DB::table('purchase_items')->insert([
                    'purchase_id' => $purchase_id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['qty'] * $item['unit_price'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
