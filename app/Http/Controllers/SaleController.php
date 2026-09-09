<?php

namespace App\Http\Controllers;

use auth;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\SaleDetail;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\StockMovement;
use App\Models\ProductVariant;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class SaleController extends Controller
{
    // ===== LIST SaleS =====
  
    public function index(Request $request)
    {
    $today = now()->toDateString();

        $sale = Sale::query()
            ->select(
                'sales.*',
                'customers.customer_name as customer_name',
                'payment_methods.payment_short_code as payment_short_code',
                'transaction_types.transaction_name as transaction_name'
            )
            ->leftJoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'sales.payment_method_id')
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'sales.transaction_type_id')
            ->whereDate('sales.sale_date', $today)
            ->orderByDesc('sales.id')
            ->paginate(10);


        $customers = customer::get();
        $paymentMethods = PaymentMethod::get();
        

        return view('sales_master.sales_listing', compact(
            'sale',
            'customers',
            'paymentMethods'
        ));
     }

    // ===== CREATE Sale =====
    public function create()
    {      
        $customers = Customer::where('status','Active')->get();
        $payment_methods = PaymentMethod::where('status','Active')->get();
        $transactionTypes = TransactionType::where('status','Active')
                                ->where('transaction_code','SAL')
                                ->get();

        // $currentShift = Shift::where('user_id', auth()->id())
        //                  ->where('status','open')
        //                  ->first();
        //$categories = Category::get();
        $categories = DB::table('categories')
            ->leftJoin('items', 'items.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(items.id) as items_count')
            )
            ->groupBy('categories.id', 'categories.name')
            ->get();
        $products = Item::select('items.*','items.pos_color as pcolor',
         'categories.name as category_name','units.unit_name',
         'brands.brand_name',
         'units.to_base',
          'categories.id as category_id')
                ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
                ->leftJoin('brands', 'items.brand_id', '=', 'brands.id')
                ->leftJoin('units', 'items.unit_id', '=', 'units.id')
                ->get();
        return view('sales_master.sales_entry',compact('products','customers','payment_methods','transactionTypes','categories'));
    }

    public function store(Request $request)
    {
        $today = Carbon::now('Asia/Yangon')->toDateTimeString();

        //dd($request->toArray());
        // Validate the request
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method_id' => 'required',
            'items' => 'required|string', // JSON string from frontend
            'extra_amount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        // Decode items JSON
        $items = json_decode($request->items, true);
        if (!is_array($items) || count($items) === 0) {
            return redirect()->back()->with('error', 'Cart is empty.');
        }

        // Begin transaction
//        DB::beginTransaction();
        //try {
            $discount = $request->extra_amount ?? 0;
            $tax = $request->tax ?? 0;
            $subTotal = $request->total_amount ?? collect($items)->sum(fn($i) => $i['qty'] * $i['price']);
            $grandTotal = $request->grand_amount ?? ($subTotal - $discount + $tax);
            $paid = $request->paid_amount;
            $balance = $paid - $grandTotal;

            // Generate voucher number
            $lastSale = Sale::latest()->first();
            $voucherNo = $lastSale ? 'POS'.str_pad($lastSale->id+1, 6, '0', STR_PAD_LEFT) : 'POS000001';

            // Save Sale
            $sale = Sale::create([
                'sale_date' => $today,
                'voucher_no' => $voucherNo,
                'customer_id' => $request->customer_id,
                'payment_method_id' => $request->payment_method_id,
                'transaction_type_id' => 1,
                'user_id' => auth()->id(),// if using auth
                'shift_id' => 1,
                'sub_total' => $subTotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paid,
                'balance_amount' => $balance,
                'status' => 'Completed'
            ]);

            // Save Sale Details
            foreach ($items as $item) {
                $product = Item::findOrFail($item['id']);
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'item_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'unit_price' => $item['price'],
                        'discount_amount' => 0,
                        'total_price' => $item['qty'] * $item['price']
                    ]);
                }
                
                $product->decrement('on_hand_qty', $item['qty']);

                    // 3️⃣ Stock movement history
                StockMovement::create([
                    'item_id'    => $item['id'],
                    'movement_type'       => 'OUT',
                    'qty'        => $item['qty'],
                    'reference_id'  => $voucherNo,
                    'note'       => 'POS Sale',
                    'balance_after'  => 0,
                    'movement_date'       => $today,
                    'maker_user_id' => '1',
                    'created_at' => $today
                ]);
                           // DB::commit();
            return redirect()->back()->with('success', 'Sale saved successfully!');
      //  } catch (\Exception $e) {
           // DB::rollBack();
           // return redirect()->back()->with('error', 'Failed to save sale: '.$e->getMessage());
       // }
    }

    public function print($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        return view('sales_master.print_receipt', compact('sale'));
    }
    public function productSearch(Request $request)
    {
        $search = $request->search;

        $query = ProductVariant::join('products', 'products.id', '=', 'product_saveSale.product_id')
            ->join('units', 'units.id', '=', 'product_saveSale.unit_id')
            ->select(
                'product_saveSale.id as variant_id',
                'products.id as product_id',
                'products.product_code',
                'products.product_name',
                'units.unit_name',
                'product_saveSale.unit_price'
            );

        // 🔍 SEARCH ONLY IF USER TYPES
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%$search%")
                ->orWhere('products.product_code', 'like', "%$search%");
            });
        }

        $products = $query->orderBy('products.product_name')->get();
        return route('sales_master.sales_entry',compact('products'));
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
    //edit 

    public function edit($sale_id)
    {
        $sale = Sale::with('saleDetails.item')->findOrFail($sale_id);
        $saleDetails = $sale->saleDetails;

        $customers = Customer::all();
        $payment_methods = PaymentMethod::all();
        $products = Item::all();

        return view('sales_master.sales_edit', compact('sale','saleDetails','customers','products','payment_methods'));
    }

    // ===== UPDATE SALE =====
    public function update(Request $request, $sale_id)
    {
        $today = Carbon::now('Asia/Yangon')->toDateTimeString();
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method_id' => 'required',
            'items' => 'required|string',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $items = json_decode($request->items,true);
        if(!is_array($items) || count($items)==0){
            return redirect()->back()->with('error','Cart is empty.');
        }

        DB::beginTransaction();
        try {
            $sale = Sale::with('saleDetails')->findOrFail($sale_id);

            // revert stock for old items
            foreach($sale->saleDetails as $oldDetail){
                $product = Item::find($oldDetail->item_id);
                if($product){
                    $product->increment('on_hand_qty', $oldDetail->quantity);
                    StockMovement::where('reference_id',$sale->voucher_no)
                        ->where('item_id',$product->id)
                        ->delete();
                }
            }

            // delete old sale details
            SaleDetail::where('sale_id',$sale->id)->delete();

            // recalc totals
            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $subTotal = collect($items)->sum(fn($i)=>$i['qty'] * $i['price']);
            $grandTotal = $subTotal - $discount + $tax;
            $paid = $request->paid_amount;
            $balance = $paid - $grandTotal;

            $sale->update([
                'customer_id' => $request->customer_id,
                'payment_method_id' => $request->payment_method_id,
                'sub_total' => $subTotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'grand_total' => $grandTotal,
                'paid_amount' => $paid,
                'balance_amount' => $balance,
                'status' => $balance >= 0 ? 'Paid' : 'Due'
            ]);

            // save new sale details and stock movement
            foreach($items as $item){
                $product = Item::findOrFail($item['id']);
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'item_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'discount_amount' => $item['discount'] ?? 0,
                    'total_price' => ($item['qty'] * $item['price']) - ($item['discount'] ?? 0)
                ]);

                $product->decrement('on_hand_qty', $item['qty']);
                StockMovement::create([
                    'item_id' => $item['id'],
                    'movement_type' => 'OUT',
                    'qty' => $item['qty'],
                    'reference_id' => $sale->voucher_no,
                    'note' => 'POS Sale (Updated)',
                    'balance_after' => $product->on_hand_qty,
                    'movement_date' => $today,
                    'maker_user_id' => auth()->id() ?? 1,
                    'created_at' => $today
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success','Sale updated successfully!');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Failed to update sale: '.$e->getMessage());
        }
    }

    // ===== DELETE SINGLE ITEM =====
    public function deleteItem($saleDetailId)
    {
        $saleDetail = SaleDetail::findOrFail($saleDetailId);
        $sale = $saleDetail->sale;
        $item = $saleDetail->item;

        DB::beginTransaction();
        try {
            if($item){
                $item->increment('on_hand_qty',$saleDetail->quantity);
                StockMovement::where('reference_id',$sale->voucher_no)
                    ->where('item_id',$item->id)
                    ->where('movement_type','OUT')
                    ->delete();
            }

            $saleDetail->delete();

            // recalc sale totals
            $subTotal = $sale->saleDetails->sum(fn($d)=>($d->quantity * $d->unit_price) - $d->discount_amount);
            $sale->update([
                'sub_total' => $subTotal,
                'grand_total' => $subTotal - $sale->discount_amount + $sale->tax_amount,
                'balance_amount' => $sale->paid_amount - ($subTotal - $sale->discount_amount + $sale->tax_amount),
                'status' => ($sale->paid_amount >= ($subTotal - $sale->discount_amount + $sale->tax_amount)) ? 'Paid' : 'Due'
            ]);

            DB::commit();
            return redirect()->back()->with('success','Item deleted successfully!');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Failed to delete item: '.$e->getMessage());
        }
    }
}
