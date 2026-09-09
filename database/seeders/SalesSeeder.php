<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $sales = [
            [
                'sale_date' => now()->subDays(3),
                'voucher_no' => 'SAL-'.Str::upper(Str::random(6)),
                'customer_id' => 1,
                'payment_type_id' => 1,      // CASH
                'transaction_type_id' => 2,  // Sale
                'user_id' => 4,              // Cashier
                'sub_total' => 500000,
                'discount_amount' => 20000,
                'tax_amount' => 30000,
                'grand_total' => 510000,
                'paid_amount' => 510000,
                'balance_amount' => 0,
                'status' => 'completed',
            ],
            [
                'sale_date' => now()->subDays(1),
                'voucher_no' => 'SAL-'.Str::upper(Str::random(6)),
                'customer_id' => 2,
                'payment_type_id' => 2,      // Card
                'transaction_type_id' => 2,
                'user_id' => 4,
                'sub_total' => 300000,
                'discount_amount' => 10000,
                'tax_amount' => 15000,
                'grand_total' => 305000,
                'paid_amount' => 200000,
                'balance_amount' => 105000,
                'status' => 'draft',
            ],
        ];

        foreach ($sales as $sale) {
            DB::table('sales')->insert(array_merge($sale, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
