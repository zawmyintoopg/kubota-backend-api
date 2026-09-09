<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['unit_name' => 'Pcs',    'to_base' => 1],
            ['unit_name' => 'Box',    'to_base' => 6],
            ['unit_name' => 'Packen', 'to_base' => 12],
            ['unit_name' => 'Dozen',  'to_base' => 12],
            ['unit_name' => 'Set',    'to_base' => 3],
        ];

        foreach ($units as $unit) {
            DB::table('units')->insert([
                'unit_name' => $unit['unit_name'],
                'to_base' => $unit['to_base'],
                'remarks' => "1 {$unit['unit_name']} = {$unit['to_base']} Pcs",
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
