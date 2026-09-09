<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\StockMovement;
use App\Models\ProductVariant;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockMovementController extends Controller
{
    // ===== LIST PURCHASES =====
   public function index()
    {
        $movements = \DB::table('stock_movements as sm')
            ->join('product_variants as pv', 'pv.id', '=', 'sm.variant_id')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('units as u', 'u.id', '=', 'pv.unit_id')
            ->select(
                'sm.id',
                'sm.variant_id',
                'sm.movement_type',
                'sm.qty',
                'sm.balance_after',
                'sm.movement_date',
                'sm.note',

                'p.name as product_name',
                'u.unit_name as unit_name'
            )
            ->orderByDesc('sm.id')
            ->get();

        $variants = \DB::table('product_variants as pv')
            ->join('products as p', 'p.id', '=', 'pv.product_id')
            ->join('units as u', 'u.id', '=', 'pv.unit_id')
            ->select(
                'pv.id',
                'p.name as product_name',
                'u.unit_name as unit_name'
            )
            ->get();

        return view('stock_master.stocks_movement', compact('movements','variants'));
    }


    // ===== CREATE PURCHASE =====
    public function create()
    {
        return view('purchase_master.purchase_entry', [
            'products' => Product::with(['variants.unit'])
                            ->where('status','active')
                            ->get(),

            'suppliers' => Supplier::where('status','Active')->get(),

            'paymentMethods' => PaymentMethod::where('status','Active')->get(),

            'transactionTypes' => TransactionType::where('status','Active')
                                    ->where('transaction_code','PUR')
                                    ->get(),
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'variant_id.*'=>'required|exists:product_variants,id',
        'movement_type.*'=>'required|in:IN,OUT,ADJUST',
        'qty.*'=>'required|numeric|min:0',
        'reference_id'=>'required|integer',
    ]);

    $variants = $request->variant_id;
    $qtys = $request->qty;
    $types = $request->movement_type;
    $referenceId = $request->reference_id;

    foreach($variants as $i => $variantId){
        $currentBalance = DB::table('stock_movements')
            ->where('variant_id', $variantId)
            ->orderByDesc('id')
            ->value('balance_after') ?? 0;

        $qty = $qtys[$i];
        $movementType = $types[$i];

        $afterBalance = $currentBalance;
        if($movementType==='IN'||$movementType==='ADJUST') $afterBalance+= $qty;
        else if($movementType==='OUT') $afterBalance-= $qty;

        if($afterBalance<0){
            return back()->with('error','Stock cannot be negative for variant ID '.$variantId);
        }

        StockMovement::create([
            'variant_id'=>$variantId,
            'movement_type'=>$movementType,
            'qty'=>$qty,
            'balance_after'=>$afterBalance,
            'note'=>$request->note ?? null,
            'movement_date'=>now(),
            'maker_user_id'=>auth()->id()??1,
            'reference_id'=>$referenceId
        ]);
    }

    return back()->with('success','Multiple stock movements added!');
}



    public function productSearch(Request $request)
    {
        $search = $request->search;

        $query = ProductVariant::join('products', 'products.id', '=', 'product_variants.product_id')
            ->join('units', 'units.id', '=', 'product_variants.unit_id')
            ->select(
                'product_variants.id as variant_id',
                'products.id as product_id',
                'products.product_code',
                'products.product_name',
                'units.unit_name',
                'product_variants.buy_price'
            );

        // 🔍 SEARCH ONLY IF USER TYPES
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%$search%")
                ->orWhere('products.product_code', 'like', "%$search%");
            });
        }

        $products = $query->orderBy('products.product_name')->get();
        return route('purchase_master.purchase_entry',compact('products'));
        // ✅ DATA EXIST CHECK
        if ($products->isEmpty()) {
            return response()->json([
                'status' => false,
                'data'   => []
            ]);
        }

        return response()->json([
            'status' => true,
            'data'   => $products
        ]);
    }

    public function print($id)
    {
        $purchase = Purchase::with('items.variant.product','items.variant.unit','supplier')
            ->findOrFail($id);
        $items = $purchase->items;

        return view('purchase_master.print', [
            'purchase' => $purchase,
            'items' => $items
        ]);
    }
    public function destroy($id)
    {
        $movement = StockMovement::find($id);

        if(!$movement){
            return back()->with('error','Stock movement not found');
        }

        // ❗ Optional: prevent deleting POS auto movements
        // if($movement->created_from_pos){
        //     return back()->with('error','Cannot delete POS movement');
        // }

        $movement->delete();

        return back()->with('success','Stock movement deleted');
    }


}
