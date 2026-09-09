<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransacionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['transaction_code' => 'PUR', 'transaction_name' => 'Purchase', 'status' => 'active'],
            ['transaction_code' => 'SALE', 'transaction_name' => 'Sale', 'status' => 'active'],
            ['transaction_code' => 'SALERET', 'transaction_name' => 'Sale Return', 'status' => 'active'],
            ['transaction_code' => 'PURCHRET', 'transaction_name' => 'Purchase Return', 'status' => 'active'],
            ['transaction_code' => 'ADJ+', 'transaction_name' => 'Stock Addition', 'status' => 'active'],
            ['transaction_code' => 'ADJ-', 'transaction_name' => 'Stock Reduction', 'status' => 'active'],
            ['transaction_code' => 'TRF', 'transaction_name' => 'Stock Transfer', 'status' => 'active'],
            ['transaction_code' => 'DISC', 'transaction_name' => 'Discount', 'status' => 'active'],
        ];

        foreach ($types as $type) {
            DB::table('transaction_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
