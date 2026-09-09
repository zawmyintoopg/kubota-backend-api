<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class ReportController extends Controller
{
    
    public function salesReport(Request $request)
    {
        $query = Sale::with(['customer','paymentMethod']); // removed saleDetails for summary

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('sale_date', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay()
            ]);
        }

        // Voucher filter
        if ($request->filled('search_by') && $request->search_by === 'voucher' && $request->filled('voucher')) {
            $query->where('voucher_no', $request->voucher);
        }

        // Customer filter
        if ($request->filled('search_by') && $request->search_by === 'customer' && $request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Payment Method filter (multiple)
        if ($request->filled('search_by') && $request->search_by === 'payment' && $request->filled('payment_method')) {
            $query->whereHas('paymentMethod', function($q) use ($request) {
                $q->whereIn('payment_short_code', (array) $request->payment_method);
            });
        }

        // Fetch Details Report (no items column in view)
        $sales = $query->orderBy('sale_date')->get();

        // Fetch Summary Report grouped by Payment Method + Date
        $summary = $query->selectRaw("
                        payment_method_id,
                        DATE(sale_date) as sale_day,
                        COUNT(*) as transaction_count,
                        SUM(grand_total) as total_amount
                    ")
                    ->groupBy('payment_method_id','sale_day')
                    ->orderBy('sale_day')
                    ->get()
                    ->map(function($item){
                        $item->payment_method_name = $item->paymentMethod->payment_short_code ?? 'N/A';
                        return $item;
                    });

        // Load filter data
        $customers = Customer::all();
        $paymentMethods = PaymentMethod::pluck('payment_short_code');

        return view('reports.sales_report', compact('sales','summary','customers','paymentMethods'));
    }

    protected function applyFilters($query, Request $request)
    {
        // Filter by date range
        if($request->filled('from_date') && $request->filled('to_date')){
            $query->whereBetween('sale_date', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay()
            ]);
        }

        // Filter by voucher
        if($request->filter_type === 'voucher' && $request->filled('voucher_no')){
            $query->where('voucher_no', $request->voucher_no);
        }

        // Filter by customer
        if($request->filter_type === 'customer' && $request->filled('customer_id')){
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by multiple payment methods
        if($request->filter_type === 'payment' && $request->filled('payment_methods')){
            $query->whereHas('paymentMethod', function($q) use ($request){
                $q->whereIn('name', $request->payment_methods);
            });
        }

        return $query;
    }
    
    // ================= PDF =================
    public function exportPdf(Request $request)
    {
        // Fetch the same data as salesReport
        $query = Sale::with(['customer','paymentMethod']);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('sale_date', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay()
            ]);
        }

        if ($request->filled('search_by') && $request->search_by === 'voucher' && $request->filled('voucher')) {
            $query->where('voucher_no', $request->voucher);
        }

        if ($request->filled('search_by') && $request->search_by === 'customer' && $request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('search_by') && $request->search_by === 'payment' && $request->filled('payment_method')) {
            $query->whereHas('paymentMethod', function($q) use ($request) {
                $q->whereIn('payment_short_code', (array) $request->payment_method);
            });
        }

        $sales = $query->orderBy('sale_date')->get();

        $summary = $query->selectRaw("
            payment_method_id,
            DATE(sale_date) as sale_day,
            COUNT(*) as transaction_count,
            SUM(grand_total) as total_amount
        ")
        ->groupBy('payment_method_id','sale_day')
        ->orderBy('sale_day')
        ->get()
        ->map(function($item){
            $item->payment_method_name = $item->paymentMethod->payment_short_code ?? 'N/A';
            return $item;
        });

        $pdf = PDF::loadView('reports.sales_report_pdf', compact('sales','summary'));
        return $pdf->download('sales_report.pdf');
    }

    // ================= EXCEL =================
    public function salesExcel(Request $request)
    {
        $sales = $this->salesReport($request)->getData()['sales'] ?? collect();

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="sales_report.xls"');

        echo view('reports.sales_table', compact('sales'));
        exit;
    }


    public function profitLossForm()
    {
        return view('reports.profit_loss_form');
    }

    public function profitLossData(Request $request)
    {
        $from = $request->from_date ?? now()->startOfMonth()->toDateString();
        $to = $request->to_date ?? now()->toDateString();
        $type = $request->type ?? 'summary'; // summary or detail

        // DETAIL DATA
        $sales = Sale::with('saleDetails.item')
            ->whereBetween('sale_date', [$from, $to])
            ->get();

        // SUMMARY DATA
        $summary = SaleDetail::selectRaw('
                DATE(sales.sale_date) as sale_day,
                SUM(sale_details.quantity * sale_details.unit_price) as revenue,
                SUM(sale_details.quantity * sale_details.cost_price) as cost,
                SUM(sale_details.quantity * sale_details.unit_price) - SUM(sale_details.quantity * sale_details.cost_price) as profit
            ')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->whereBetween('sales.sale_date', [$from, $to])
            ->groupBy('sale_day')
            ->get();

        return view('profit_loss_form', compact('sales', 'summary', 'from', 'to', 'type'));
    }
}
