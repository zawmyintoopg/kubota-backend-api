<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('brands')->insert([
            ['brand_name' => 'Zara',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'H&M',            'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Uniqlo',         'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Nike',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Adidas',         'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Levi\'s',        'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Puma',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Forever 21',     'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Gap',            'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'Tommy Hilfiger', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],

            /* ================= MODERN MYANMAR BRANDS (10) ================= */
            ['brand_name' => 'မြန်မာစတိုင်',        'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'ယိုးဒယား',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'မိုဒန်ဖက်ရှင်',       'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'လတ်တလောစတိုင်',      'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'ဖက်ရှင်မိတ်',         'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'ရှော့ပြတ်စတိုင်',       'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'စမတ်ဝတ်စုံ',         'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'အင်းစတိုင်',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'လူငယ်ဖက်ရှင်',       'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['brand_name' => 'တီမိုဒီဖက်ရှင်',      'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
        ]);  
    }
}
