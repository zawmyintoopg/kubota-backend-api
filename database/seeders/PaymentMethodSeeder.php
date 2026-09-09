<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['payment_short_code' => 'CASH', 'payment_description' => 'Cash Payment', 'status' => 'active'],
            ['payment_short_code' => 'CARD', 'payment_description' => 'Credit/Debit Card', 'status' => 'active'],
            ['payment_short_code' => 'BANK', 'payment_description' => 'Bank Transfer', 'status' => 'active'],
            ['payment_short_code' => 'MOMO', 'payment_description' => 'Mobile Wallet (MOMO)', 'status' => 'active'],
            ['payment_short_code' => 'KBZ',  'payment_description' => 'KBZ Pay', 'status' => 'active'],
            ['payment_short_code' => 'WAVE', 'payment_description' => 'Wave Pay', 'status' => 'active'],
        ];

        foreach ($methods as $method) {
            DB::table('payment_methods')->insert(array_merge($method, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
