<?php

namespace App\Http\Controllers;

use auth;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Shift;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\purchase;
use App\Models\Supplier;
use App\Models\purchaseItem;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\StockMovement;
use App\Models\ProductVariant;
use App\Models\purchaseDetail;
use App\Models\TransactionType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\View\Components\Alert;



class PurchaseController extends Controller
{
    // ===== LIST purchaseS =====
  
    public function index(Request $request)
    {
    $today = now()->toDateString();

        $purchase = Purchase::query()
            ->select(
                'purchases.*',
                'suppliers.supplier_name as supplier_name',
                'payment_methods.payment_short_code as payment_short_code',
                'transaction_types.transaction_name as transaction_name'
            )
            ->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'purchases.payment_method_id')
            ->leftJoin('transaction_types', 'transaction_types.id', '=', 'purchases.transaction_type_id')
            ->whereDate('purchases.purchase_date', $today)
            ->orderByDesc('purchases.id')
            ->paginate(10);


        $suppliers = supplier::get();
        $paymentMethods = PaymentMethod::get();
        

        return view('purchase_master.purchase_listing', compact(
            'purchase',
            'suppliers',
            'paymentMethods'
        ));
     }

    // ===== CREATE purchase =====
    public function create()
    {      
        //dd('user');
        $suppliers = Supplier::where('status','Active')->get();
        $payment_methods = PaymentMethod::where('status','Active')->get();
        $transactionTypes = TransactionType::where('status','Active')
                                ->where('transaction_code','PUR')
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
        return view('purchase_master.purchase_entry',compact('products','suppliers','payment_methods','transactionTypes','categories'));
    }

    public function store(Request $request)
    {
        $today = Carbon::now('Asia/Yangon')->toDateTimeString();

       
        // Validate the request
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
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
            $lastPurchase = Purchase::latest()->first();
            $voucherNo = $lastPurchase ? 'POS_PUR'.str_pad($lastPurchase->id+1, 6, '0', STR_PAD_LEFT) : 'POS000001';
             //dd($request->toArray());
            // Save purchase
            $purchase = Purchase::create([
                'purchase_date' => $today,
                'voucher_no' => $voucherNo,
                'supplier_id' => $request->supplier_id,
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
            
            // Save purchase Details
            foreach ($items as $item) {
                $product = Item::findOrFail($item['id']);
                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'item_id' => $item['id'],
                        'quantity' => $item['qty'],
                        'unit_price' => $item['price'],
                        'discount_amount' => 0,
                        'total_price' => $item['qty'] * $item['price']
                    ]);
                }
                
                $product->increment('on_hand_qty', $item['qty']);

                    // 3️⃣ Stock movement history
                StockMovement::create([
                    'item_id'    => $item['id'],
                    'movement_type'       => 'IN',
                    'qty'        => $item['qty'],
                    'reference_id'  => $voucherNo,
                    'note'       => 'POS purchase',
                    'balance_after'  => 0,
                    'movement_date'       => $today,
                    'maker_user_id' => '1',
                    'created_at' => $today
                ]);
           // Alert::success('Success', 'Purchase Save successfully');
                           // DB::commit();
            return redirect()->back()->with('success', 'purchase saved successfully!');
      //  } catch (\Exception $e) {
           // DB::rollBack();
           // return redirect()->back()->with('error', 'Failed to save purchase: '.$e->getMessage());
       // }
    }

    public function print($id)
    {
        $purchase = purchase::with('items')->findOrFail($id);
        return view('purchases_master.print_receipt', compact('purchase'));
    }
    public function productSearch(Request $request)
    {
        $search = $request->search;

        $query = ProductVariant::join('products', 'products.id', '=', 'product_savepurchase.product_id')
            ->join('units', 'units.id', '=', 'product_savepurchase.unit_id')
            ->select(
                'product_savepurchase.id as variant_id',
                'products.id as product_id',
                'products.product_code',
                'products.product_name',
                'units.unit_name',
                'product_savepurchase.unit_price'
            );

        // 🔍 SEARCH ONLY IF USER TYPES
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%$search%")
                ->orWhere('products.product_code', 'like', "%$search%");
            });
        }

        $products = $query->orderBy('products.product_name')->get();
        return route('purchases_master.purchases_entry',compact('products'));
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

    public function edit($purchase_id)
    {
        $purchase = purchase::with('purchaseDetails.item')->findOrFail($purchase_id);
        $purchaseDetails = $purchase->purchaseDetails;

        $suppliers = Customer::all();
        $payment_methods = PaymentMethod::all();
        $products = Item::all();

        return view('purchases_master.purchases_edit', compact('purchase','purchaseDetails','suppliers','products','payment_methods'));
    }

    // ===== UPDATE purchase =====
    public function update(Request $request, $purchase_id)
    {
        $today = Carbon::now('Asia/Yangon')->toDateTimeString();
        $request->validate([
            'customer_id' => 'nullable|exists:suppliers,id',
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
            $purchase = purchase::with('purchaseDetails')->findOrFail($purchase_id);

            // revert stock for old items
            foreach($purchase->purchaseDetails as $oldDetail){
                $product = Item::find($oldDetail->item_id);
                if($product){
                    $product->increment('on_hand_qty', $oldDetail->quantity);
                    StockMovement::where('reference_id',$purchase->voucher_no)
                        ->where('item_id',$product->id)
                        ->delete();
                }
            }

            // delete old purchase details
            purchaseDetail::where('purchase_id',$purchase->id)->delete();

            // recalc totals
            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $subTotal = collect($items)->sum(fn($i)=>$i['qty'] * $i['price']);
            $grandTotal = $subTotal - $discount + $tax;
            $paid = $request->paid_amount;
            $balance = $paid - $grandTotal;

            $purchase->update([
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

            // save new purchase details and stock movement
            foreach($items as $item){
                $product = Item::findOrFail($item['id']);
                purchaseDetail::create([
                    'purchase_id' => $purchase->id,
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
                    'reference_id' => $purchase->voucher_no,
                    'note' => 'POS purchase (Updated)',
                    'balance_after' => $product->on_hand_qty,
                    'movement_date' => $today,
                    'maker_user_id' => auth()->id() ?? 1,
                    'created_at' => $today
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success','purchase updated successfully!');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Failed to update purchase: '.$e->getMessage());
        }
    }

    // ===== DELETE SINGLE ITEM =====
    public function deleteItem($purchaseDetailId)
    {
        $purchaseDetail = purchaseDetail::findOrFail($purchaseDetailId);
        $purchase = $purchaseDetail->purchase;
        $item = $purchaseDetail->item;

        DB::beginTransaction();
        try {
            if($item){
                $item->increment('on_hand_qty',$purchaseDetail->quantity);
                StockMovement::where('reference_id',$purchase->voucher_no)
                    ->where('item_id',$item->id)
                    ->where('movement_type','OUT')
                    ->delete();
            }

            $purchaseDetail->delete();

            // recalc purchase totals
            $subTotal = $purchase->purchaseDetails->sum(fn($d)=>($d->quantity * $d->unit_price) - $d->discount_amount);
            $purchase->update([
                'sub_total' => $subTotal,
                'grand_total' => $subTotal - $purchase->discount_amount + $purchase->tax_amount,
                'balance_amount' => $purchase->paid_amount - ($subTotal - $purchase->discount_amount + $purchase->tax_amount),
                'status' => ($purchase->paid_amount >= ($subTotal - $purchase->discount_amount + $purchase->tax_amount)) ? 'Paid' : 'Due'
            ]);

            DB::commit();
            return redirect()->back()->with('success','Item deleted successfully!');
        } catch(\Exception $e){
            DB::rollBack();
            return redirect()->back()->with('error','Failed to delete item: '.$e->getMessage());
        }
    }
}
