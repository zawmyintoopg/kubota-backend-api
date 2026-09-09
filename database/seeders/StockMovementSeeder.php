<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [];
        $balance = [];

        // Purchase Items (in = add stock)
        $purchaseItems = [
            ['variant_id'=>1,'qty'=>10,'source_type'=>1,'source_id'=>1], // Purchase 1
            ['variant_id'=>3,'qty'=>30,'source_type'=>1,'source_id'=>1], // Purchase 1, Box=6Pcs, 5*6=30
            ['variant_id'=>5,'qty'=>8,'source_type'=>1,'source_id'=>2],  // Purchase 2
            ['variant_id'=>7,'qty'=>12,'source_type'=>1,'source_id'=>2], // Purchase 2
            ['variant_id'=>9,'qty'=>20,'source_type'=>1,'source_id'=>3], // Purchase 3
            ['variant_id'=>11,'qty'=>18,'source_type'=>1,'source_id'=>3],// Purchase 3
        ];

        foreach($purchaseItems as $item){
            $vid = $item['variant_id'];
            $inQty = $item['qty'];

            $balance[$vid] = ($balance[$vid] ?? 0) + $inQty;

            $stocks[] = [
                'product_variant_id' => $vid,
                'type' => 'in',
                'qty' => $inQty,
                'source_type' => $item['source_type'],
                'source_id' => $item['source_id'],
                'balance_after' => $balance[$vid],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sale Items (out = reduce stock)
        $saleItems = [
            ['variant_id'=>1,'qty'=>5,'source_type'=>2,'source_id'=>1], // Sale 1
            ['variant_id'=>3,'qty'=>12,'source_type'=>2,'source_id'=>1], // Sale 1
            ['variant_id'=>5,'qty'=>3,'source_type'=>2,'source_id'=>2],  // Sale 2
            ['variant_id'=>7,'qty'=>6,'source_type'=>2,'source_id'=>2],  // Sale 2
        ];

        foreach($saleItems as $item){
            $vid = $item['variant_id'];
            $outQty = $item['qty'];

            $balance[$vid] = ($balance[$vid] ?? 0) - $outQty;

            $stocks[] = [
                'product_variant_id' => $vid,
                'type' => 'out',
                'qty' => $outQty,
                'source_type' => $item['source_type'],
                'source_id' => $item['source_id'],
                'balance_after' => $balance[$vid],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('stock_movement')->insert($stocks);
    }
}
