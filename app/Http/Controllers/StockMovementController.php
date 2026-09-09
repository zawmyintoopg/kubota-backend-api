<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StockMovement;

class StockMovementController extends Controller
{
    public function index(){
        $movements = StockMovement::with('variant.product','variant.unit')->orderBy('movement_date','desc')->get();
        $variants = DB::table('product_variants as pv')
            ->join('products as p','p.id','=','pv.product_id')
            ->join('units as u','u.id','=','pv.unit_id')
            ->select('pv.id','p.name as product_name','u.unit_name as unit_name')
            
            ->get();

        return view('stock_master.stocks_movement', compact('movements','variants'));
    }

    public function store(Request $request){
        $request->validate([
            'variant_id.*'=>'required|exists:product_variants,id',
            'movement_type.*'=>'required|in:IN,OUT,ADJUST',
            'qty.*'=>'required|numeric|min:0',
            'reference_id'=>'required|integer',
        ]);

        foreach($request->variant_id as $i => $variantId){
            $currentBalance = DB::table('stock_movements')
                ->where('variant_id',$variantId)
                ->orderByDesc('id')
                ->value('balance_after') ?? 0;

            $qty = $request->qty[$i];
            $type = $request->movement_type[$i];
            $afterBalance = $currentBalance;
            if($type==='IN'||$type==='ADJUST') $afterBalance += $qty;
            else $afterBalance -= $qty;

            if($afterBalance<0) return back()->with('error','Stock cannot be negative for variant '.$variantId);

            StockMovement::create([
                'variant_id'=>$variantId,
                'movement_type'=>$type,
                'qty'=>$qty,
                'balance_after'=>$afterBalance,
                'movement_date'=>now(),
                'note'=>$request->note[$i]??null,
                'maker_user_id'=>auth()->id()??1,
                'reference_id'=>$request->reference_id
            ]);
        }

        return back()->with('success','Stock movements added!');
    }

    public function destroy($id){
        $m = StockMovement::findOrFail($id);
        if($m->reference_id) return back()->with('error','Cannot delete reference-linked movement!');
        $m->delete();
        return back()->with('success','Movement deleted!');
    }

    public function bulkDelete(Request $request){
        $request->validate(['ids'=>'required|array']);
        $movements = StockMovement::whereIn('id',$request->ids)->get();
        foreach($movements as $m){
            if(!$m->reference_id) $m->delete();
        }
        return back()->with('success','Selected movements deleted!');
    }

    public function edit($id){
        $movement = StockMovement::findOrFail($id);
        $variants = DB::table('product_variants as pv')
            ->join('products as p','p.id','=','pv.product_id')
            ->join('units as u','u.id','=','pv.unit_id')
            ->select('pv.id','p.name as product_name','u.name as unit_name')
            ->get();
        return view('stock_movements.edit', compact('movement','variants'));
    }

    public function update(Request $request,$id){
        $m = StockMovement::findOrFail($id);
        $request->validate([
            'variant_id'=>'required|exists:product_variants,id',
            'movement_type'=>'required|in:IN,OUT,ADJUST',
            'qty'=>'required|numeric|min:0',
            'reference_id'=>'required|integer'
        ]);

        $currentBalance = DB::table('stock_movements')
            ->where('variant_id',$request->variant_id)
            ->where('id','<>',$id)
            ->orderByDesc('id')
            ->value('balance_after') ?? 0;

        $afterBalance = $currentBalance;
        if($request->movement_type==='IN'||$request->movement_type==='ADJUST') $afterBalance += $request->qty;
        else $afterBalance -= $request->qty;

        if($afterBalance<0) return back()->with('error','Stock cannot be negative!');

        $m->update([
            'variant_id'=>$request->variant_id,
            'movement_type'=>$request->movement_type,
            'qty'=>$request->qty,
            'balance_after'=>$afterBalance,
            'note'=>$request->note??$m->note,
            'reference_id'=>$request->reference_id
        ]);

        return back()->with('success','Movement updated!');
    }

    public function bulkEditJson(Request $request){
        $ids = explode(',', $request->ids);
        $movements = StockMovement::whereIn('id',$ids)->get()->map(function($m){
            $currentStock = DB::table('stock_movements')
                ->where('variant_id',$m->variant_id)
                ->orderByDesc('id')
                ->value('balance_after') ?? 0;
            return [
                'id'=>$m->id,
                'product_name'=>$m->variant->product->name,
                'unit_name'=>$m->variant->unit->name,
                'movement_type'=>$m->movement_type,
                'qty'=>$m->qty,
                'note'=>$m->note,
                'current_stock'=>$currentStock
            ];
        });
        return response()->json(['movements'=>$movements]);
    }

    public function bulkUpdate(Request $request){
        $request->validate(['ids'=>'required|array','movement_type'=>'required','qty'=>'required']);
        foreach($request->ids as $i => $id){
            $m = StockMovement::find($id);
            if(!$m || $m->reference_id) continue;

            $currentBalance = DB::table('stock_movements')
                ->where('variant_id',$m->variant_id)
                ->where('id','<>',$id)
                ->orderByDesc('id')
                ->value('balance_after') ?? 0;

            $afterBalance = $currentBalance;
            $type = $request->movement_type[$i];
            $qty = $request->qty[$i];
            if($type==='IN'||$type==='ADJUST') $afterBalance += $qty;
            else $afterBalance -= $qty;

            if($afterBalance<0) continue;

            $m->update([
                'movement_type'=>$type,
                'qty'=>$qty,
                'balance_after'=>$afterBalance,
                'note'=>$request->note[$i]??$m->note
            ]);
        }
        return back()->with('success','Selected movements updated!');
    }

    public function variantStock($id){
        $balance = DB::table('stock_movements')
            ->where('variant_id',$id)
            ->orderByDesc('id')
            ->value('balance_after') ?? 0;

        $unit_name = DB::table('product_variants as pv')
            ->join('units as u','u.id','=','pv.unit_id')
            ->where('pv.id',$id)
            ->value('u.name');

        return response()->json(['balance'=>$balance,'unit_name'=>$unit_name]);
    }
}
