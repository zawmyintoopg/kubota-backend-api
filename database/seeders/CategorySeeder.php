<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'T-Shirts',             'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shirts',               'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pants & Jeans',        'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dresses',              'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Skirts',               'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shorts',               'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jackets',              'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coats',                'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hoodies & Sweatshirts','status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sportswear',           'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],

            /* ================= MYANMAR (10 Unique) ================= */
            ['name' => 'အမျိုးသားအဝတ်အစား',      'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'အမျိုးသမီးအဝတ်အစား',      'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ကလေးအဝတ်အစား',            'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'အတွင်းခံအဝတ်အစား',        'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'အိမ်သုံးအဝတ်အစား',        'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ပွဲသုံးအဝတ်အစား',          'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ရုံးသုံးအဝတ်အစား',          'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'အပူအင်္ကျီ',                 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'အားကစားအဝတ်အစား',          'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ရိုးရာအဝတ်အစား',            'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
     ]);
    }
}
