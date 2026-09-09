<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'customer_code' => 'CUS001',
                'customer_name' => 'Aung Myint Shop',
                'phone' => '09501234567',
                'address' => 'No. 12, Yangon, Myanmar',
                'contact_person' => 'Aung Myint',
                'status' => 'active',
            ],
            [
                'customer_code' => 'CUS002',
                'customer_name' => 'Thida Fashion',
                'phone' => '09507654321',
                'address' => 'No. 45, Yangon, Myanmar',
                'contact_person' => 'Thida Win',
                'status' => 'active',
            ],
            [
                'customer_code' => 'CUS003',
                'customer_name' => 'Mandalay Clothes',
                'phone' => '09509876543',
                'address' => 'Mandalay Industrial Zone',
                'contact_person' => 'Ko Htet',
                'status' => 'active',
            ],
            [
                'customer_code' => 'CUS004',
                'customer_name' => 'Modern Fashion Hub',
                'phone' => '09507651234',
                'address' => 'No. 10, Bahan, Yangon',
                'contact_person' => 'Ei Mon',
                'status' => 'active',
            ],
        ];

        foreach ($customers as $customer) {
            DB::table('customers')->insert(array_merge($customer, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
