<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockBalanceExport;

class StockController extends Controller
{
  public function stockBalance(Request $request)
{
    $search = $request->input('search');
    $filter = $request->input('filter'); // "Low Stock" or "OK"

    $stocksQuery = Stock::with('variant')
        ->when($search, function($query, $search) {
            $query->whereHas('variant', function($q) use ($search) {
                $q->where('product_id', 'like', "%{$search}%");
            });
        });

    // Get all stocks for charts and filtering
    $allStocks = $stocksQuery->get()->map(function($stock){
        $stock->converted_quantity = $stock->quantity * ($stock->variant->conversion_rate ?? 1);
        $stock->total_value = $stock->converted_quantity * ($stock->variant->unit_price ?? 0);
        $stock->is_below_reorder = $stock->converted_quantity <= ($stock->variant->reorder_point ?? 0);
        return $stock;
    });

    // Apply filter if clicked from chart
    if($filter){
        $allStocks = $allStocks->filter(function($stock) use ($filter){
            if($filter === 'Low Stock') return $stock->is_below_reorder;
            if($filter === 'OK') return !$stock->is_below_reorder;
            return true;
        });
    }

    // Pagination
    $perPage = 20;
    $page = $request->input('page', 1);
    $stocks = $allStocks->forPage($page, $perPage);
    $stocks = new \Illuminate\Pagination\LengthAwarePaginator(
        $stocks, 
        $allStocks->count(), 
        $perPage, 
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    // Chart data
    $totalValue = $allStocks->sum('total_value');
    $lowStockProducts = $allStocks->filter(fn($s) => $s->is_below_reorder)->pluck('variant.product_id')->toArray();
    $lowStockCount = count($lowStockProducts);

    // Export
    if ($request->export == 'pdf') {
        $pdf = PDF::loadView('stock_master.stock_balance_pdf', ['stocks'=>$allStocks]);
        return $pdf->download('stock_balance.pdf');
    }

    if ($request->export == 'excel') {
        return Excel::download(new StockBalanceExport($allStocks), 'stock_balance.xlsx');
    }

    // AJAX return partial
    if($request->ajax()){
        return view('stock.stock_table_partial', compact('stocks','totalValue','lowStockCount','lowStockProducts'))->render();
    }

    return view('stock_master.stock_dashboard', compact('stocks','search','totalValue','lowStockCount','lowStockProducts'));
}

}
