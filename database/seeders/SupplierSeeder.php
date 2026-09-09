<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'supplier_code' => 'SUP001',
                'supplier_name' => 'Myanmar Textiles Co.',
                'phone' => '09501234567',
                'address' => 'No. 12, Yangon, Myanmar',
                'contact_person' => 'Aung Kyaw',
                'status' => 'active',
            ],
            [
                'supplier_code' => 'SUP002',
                'supplier_name' => 'Yangon Fashion Supplier',
                'phone' => '09507654321',
                'address' => 'No. 45, Yangon, Myanmar',
                'contact_person' => 'Thida Win',
                'status' => 'active',
            ],
            [
                'supplier_code' => 'SUP003',
                'supplier_name' => 'Global Fashion Ltd.',
                'phone' => '09509876543',
                'address' => 'Mandalay Industrial Zone',
                'contact_person' => 'Ko Htet',
                'status' => 'active',
            ],
            [
                'supplier_code' => 'SUP004',
                'supplier_name' => 'Modern Apparel',
                'phone' => '09507651234',
                'address' => 'No. 10, Bahan, Yangon',
                'contact_person' => 'Ei Mon',
                'status' => 'active',
            ],
        ];

        foreach ($suppliers as $supplier) {
            DB::table('suppliers')->insert(array_merge($supplier, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
