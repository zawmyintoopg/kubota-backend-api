<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------
        // DATE PARSING
        // -------------------------------
        $selectedDate = today();
        if ($request->has('date') && !empty($request->date)) {
            try {
                $selectedDate = Carbon::parse($request->date);
            } catch (\Exception $e) {
                Log::error('Invalid date: '.$request->date);
            }
        }

        // -------------------------------
        // TOTALS
        // -------------------------------
        $totalSales = Sale::whereDate('sale_date', $selectedDate)->sum('grand_total');
        $totalPurchase = 0;//Purchase::whereDate('purchase_date', $selectedDate)->sum('grand_total');

        // -------------------------------
        // Transactions
        // -------------------------------
        $transactions = Sale::join('sale_details','sale_details.sale_id','=','sales.id')
            ->join('items','sale_details.item_id','=','items.id')
            ->select(
                'sales.sale_date as created_at',
                DB::raw("'sale' as type"),
                'items.item_name as product_name',
                DB::raw('SUM(sale_details.quantity) as total_qty'),
                DB::raw('SUM(sale_details.quantity * sale_details.unit_price) as total_amount')
            )
            ->whereDate('sales.sale_date', $selectedDate)
            ->groupBy('items.item_name','sales.sale_date')
            ->get();

        // -------------------------------
        // CHART DATA
        // -------------------------------
        $chartData = [
            'labels' => [$selectedDate->toDateString()],
            'sales' => [$totalSales],
            'purchases' => [$totalPurchase],
        ];

        // Sales by payment method (donut chart)
        $salesByPayment = Sale::join('payment_methods','sales.payment_method_id','=','payment_methods.id')
            ->whereDate('sales.sale_date', $selectedDate)
            ->select('payment_methods.payment_short_code as name', DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_methods.payment_short_code')
            ->get();

        $salesByPaymentData = [
            'labels' => $salesByPayment->pluck('name'),
            'amounts' => $salesByPayment->pluck('total')
        ];

        // Transactions by customer (pie chart)
        $transactionsByCustomer = Sale::join('customers','sales.customer_id','=','customers.id')
            ->whereDate('sales.sale_date', $selectedDate)
            ->select('customers.customer_name', DB::raw('COUNT(*) as count'))
            ->groupBy('customers.customer_name')
            ->get();

        $transactionsByCustomerData = [
            'labels' => $transactionsByCustomer->pluck('customer_name'),
            'counts' => $transactionsByCustomer->pluck('count')
        ];

        // -------------------------------
        // BEST SELLERS
        // -------------------------------
        $bestSellers = Sale::join('sale_details','sale_details.sale_id','=','sales.id')
            ->join('items','sale_details.item_id','=','items.id')
            ->whereDate('sales.sale_date', $selectedDate)
            ->select('items.item_name', DB::raw('SUM(sale_details.quantity) as total_qty'))
            ->groupBy('items.item_name')
            ->orderByDesc('total_qty')
            ->get();

        // -------------------------------
        // LOW STOCK PRODUCTS
        // -------------------------------
        $lowStockProducts = Item::select('items.*','items.item_name as product_name')           
         ->where('items.on_hand_qty','<=',5)
            ->orderBy('items.on_hand_qty','asc')
            ->get();

        // -------------------------------
        // AJAX RESPONSE
        // -------------------------------
        if ($request->ajax()) {
            return response()->json([
                'totalPurchase' => $totalPurchase,
                'totalSales' => $totalSales,
                'transactions' => view('partials.transactions_table', compact('transactions'))->render(),
                'chartData' => $chartData,
                'salesByPayment' => $salesByPaymentData,
                'transactionsByCustomer' => $transactionsByCustomerData,
                'bestSellers' => $bestSellers,
                'lowStockProducts' => $lowStockProducts
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
            'salesByPaymentData',
            'transactionsByCustomerData',
            'bestSellers',
            'lowStockProducts'
        ));
    }

    // -------------------------------
    // PDF EXPORT
    // -------------------------------
    public function downloadDashboardPdf(Request $request)
    {
        $lowStockProducts = Item::select('stocks.*','products.item_name as product_name')
        
            ->where('stocks.on_hand_qty','<=',5)
            ->orderBy('stocks.on_hand_qty','asc')
            ->get();

        $chartsImages = $request->charts ?? [];

        $pdf = Pdf::loadView('main.dashboard_pdf', compact('lowStockProducts','chartsImages'));

        return $pdf->download('dashboard_report_'.now()->format('Ymd_His').'.pdf');
    }
}
