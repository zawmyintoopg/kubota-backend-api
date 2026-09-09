<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceHistoryController extends Controller
{
        public function index()
        {
            $histories = DB::table('product_price_histories as h')
                ->join('products as p', 'p.id', '=', 'h.product_id')
                ->join('product_variants as pv', 'pv.id', '=', 'h.product_variant_id')
                ->join('units as u', 'u.id', '=', 'pv.unit_id')
                ->leftJoin('users as us', 'us.id', '=', 'h.changed_by')
                ->select(
                    'h.id',
                    'p.product_code',
                    'p.name as product_name',
                    'u.unit_name',
                    'h.old_price',
                    'h.new_price',
                    'h.changed_at',
                    DB::raw("COALESCE(us.name,'System') as changed_by")
                )
                ->orderByDesc('h.changed_at')
                ->paginate(20);

            return view('product_master.price_history', compact('histories'));
        }
    

    public function rollback($id)
    {
        $history = DB::table('product_price_histories')
            ->where('id', $id)
            ->first();

        if (!$history) {
            return back()->withErrors('History not found');
        }

        DB::transaction(function () use ($history) {

            // Restore old buy price
            DB::table('product_variants')
                ->where('id', $history->product_variant_id)
                ->update([
                    'buyprice' => $history->old_price
                ]);

            // Log rollback (NO USER)
            DB::table('product_price_histories')->insert([
                'product_id'         => $history->product_id,
                'product_variant_id' => $history->product_variant_id,
                'purchase_id'        => null,
                'old_price'          => $history->new_price,
                'new_price'          => $history->old_price,
                'action'             => 'rollback',
                'changed_by'         => null, // ✅ SKIPPED
                'changed_at'         => now(),
            ]);
        });

        return back()->with('success', 'Price rolled back successfully');
        }
    }