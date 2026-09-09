<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------
        // SAFE DATE PARSING
        // -------------------------------
        $selectedDate = today();
        
        if ($request->has('date') && !empty($request->date)) {
            try {
                $selectedDate = Carbon::parse($request->date);
            } catch (\Exception $e) {
                Log::error('Invalid date: '.$request->date);
            }
        }

        $transactionType = $request->transaction_type ?? 'all';

        // -------------------------------
        // TOTALS
        // -------------------------------
        $totalSales = 0;
        $totalPurchase = 0;

        if ($transactionType === 'sale') {
            $totalSales = Sale::whereDate('sale_date', $selectedDate)->sum('grand_total');
        } elseif ($transactionType === 'purchase') {
            $totalPurchase = Purchase::whereDate('purchase_date', $selectedDate)->sum('grand_total');
        } else {
            $totalSales = Sale::whereDate('sale_date', $selectedDate)->sum('grand_total');
            $totalPurchase = Purchase::whereDate('purchase_date', $selectedDate)->sum('grand_total');
        }

        // -------------------------------
        // TRANSACTIONS
        // -------------------------------
        $transactions = collect();

        if ($transactionType === 'sale' || $transactionType === 'all') {
            $sales = Sale::select(
                'sales.sale_date as created_at',
                DB::raw("'sale' as type"),
                'items.item_name as product_name',
                'categories.name as category_name',
                'brands.brand_name as brand_name',
                'sale_details.quantity',
                'sale_details.unit_price',
                DB::raw('(sale_details.quantity * sale_details.unit_price) as total')
            )
            ->join('sale_details','sale_details.sale_id','=','sales.id')
            ->join('items','sale_details.item_id','=','items.id')
            ->join('categories','items.category_id','=','categories.id')
            ->join('brands','items.brand_id','=','brands.id')
            ->whereDate('sales.sale_date',$selectedDate)
            ->get();

            $transactions = $transactions->merge($sales);
        }

        if ($transactionType === 'purchase' || $transactionType === 'all') {
            $purchases = Purchase::select(
                'purchases.purchase_date as created_at',
                DB::raw("'purchase' as type"),
                'items.item_name as product_name',
                'categories.name as category_name',
                'brands.brand_name as brand_name',
                'purchase_details.quantity',
                'purchase_details.unit_price',
                DB::raw('(purchase_details.quantity * purchase_details.unit_price) as total')
            )
            ->join('purchase_details','purchase_details.purchase_id','=','purchases.id')
            ->join('items','purchase_details.item_id','=','items.id')         
            ->join('categories','items.category_id','=','categories.id')
            ->join('brands','items.brand_id','=','brands.id')
            ->whereDate('purchases.purchase_date',$selectedDate)
            ->get();

            $transactions = $transactions->merge($purchases);
        }

        // -------------------------------
        // CHART DATA
        // -------------------------------
        $salesTotal = Sale::join('sale_details','sale_details.sale_id','=','sales.id')
            ->whereDate('sales.sale_date',$selectedDate)
            ->sum(DB::raw('sale_details.quantity * sale_details.unit_price'));

        $purchaseTotal = Purchase::join('purchase_details','purchase_details.purchase_id','=','purchases.id')
            ->whereDate('purchases.purchase_date',$selectedDate)
            ->sum(DB::raw('purchase_details.quantity * purchase_details.unit_price'));

        $chartData = [
            'labels' => [$selectedDate->toDateString()],
            'sales' => [$salesTotal],
            'purchases' => [$purchaseTotal]
        ];

        // -------------------------------
        // LOW STOCK ALERT
        // -------------------------------
        $lowStockProducts = Stock::select('stocks.*','products.name as product_name')
            ->join('products','stocks.product_id','=','products.id')
            ->where('stocks.onhand_qty','<=',5)
            ->orderBy('stocks.onhand_qty','asc')
            ->get();

        // -------------------------------
        // PROFIT & LOSS DATA
        // -------------------------------
        $profitLossData = collect();
        $salesItems = Sale::join('sale_details','sale_details.sale_id','=','sales.id')
            ->join('items','sale_details.item_id','=','items.id')
            ->join('purchase_details', function($join){
                $join->on('purchase_details.item_id','=','items.id');
            })
            ->select(
                'items.item_name as product_name',
                'items.id as item_id',
                DB::raw('SUM(sale_details.quantity) as sold_qty'),
                DB::raw('AVG(sale_details.unit_price) as sale_price_avg'),
                DB::raw('AVG(purchase_details.unit_price) as purchase_price_avg')
            )
            ->whereDate('sales.sale_date', $selectedDate)
            ->groupBy('items.id','items.item_name')
            ->get();

        foreach($salesItems as $item){
            $profit = ($item->sale_price_avg - $item->purchase_price_avg) * $item->sold_qty;
            $profitLossData->push([
                'product_name' => $item->product_name,
                'sold_qty' => $item->sold_qty,
                'sale_price_avg' => $item->sale_price_avg,
                'purchase_price_avg' => $item->purchase_price_avg,
                'profit' => $profit
            ]);
        }

        // -------------------------------
        // AJAX RESPONSE
        // -------------------------------
        if ($request->ajax()) {
            $profitLossHtml = view('partials.profit_loss_table', compact('profitLossData'))->render();
            $transactionsHtml = view('partials.transactions_table', compact('transactions'))->render();

            return response()->json([
                'totalPurchase' => $totalPurchase,
                'totalSales' => $totalSales,
                'transactions' => $transactionsHtml,
                'profitLossHtml' => $profitLossHtml,
                'chartData' => $chartData
            ]);
        }

        // -------------------------------
        // RETURN VIEW
        // -------------------------------
        return view('main.admin', compact(
            'totalPurchase',
            'totalSales',
            'transactions',
            'chartData',
            'lowStockProducts',
            'transactionType',
            'selectedDate',
            'profitLossData'
        ));
    }
}
