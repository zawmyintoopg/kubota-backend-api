<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type','daily');
        $date = $request->get('date', now()->toDateString());

        $report = $this->getReport($type, $date);

        return view('reports.stock_movement_report', compact('report','type','date'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type','daily');
        $date = $request->get('date', now()->toDateString());
        $format = $request->get('format','csv');

        $report = $this->getReport($type, $date);

        if($format == 'csv'){
            $filename = "stock_report_{$date}.csv";
            $headers = ['Content-Type' => 'text/csv'];
            $callback = function() use ($report){
                $file = fopen('php://output','w');
                fputcsv($file, ['Product','Unit','Variant','Opening','IN','OUT','Closing']);
                foreach($report as $r){
                    fputcsv($file, [
                        $r->product_name,
                        $r->unit_name ?? '-',
                        $r->variant_id,
                        0, // opening
                        $r->total_in,
                        $r->total_out,
                        $r->closing_stock
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers)
                   ->header('Content-Disposition', "attachment; filename={$filename}");
        }

        if($format == 'pdf'){
            $pdf = \PDF::loadView('reports.stock_movement_report_pdf', compact('report','type','date'));
            return $pdf->download("stock_report_{$date}.pdf");
        }
    }

    // -----------------------------
    // PRIVATE METHOD FOR REUSABLE QUERY
    // -----------------------------
    private function getReport($type, $date)
    {
        if($type === 'monthly'){
            $start = date('Y-m-01', strtotime($date));
            $end   = date('Y-m-t', strtotime($date));
        } else {
            $start = $date;
            $end   = $date;
        }

        return DB::table('stock_movements as sm')
            ->join('product_variants as pv','pv.id','=','sm.variant_id')
            ->join('products as p','p.id','=','pv.product_id')
            ->join('units as u','u.id','=','pv.unit_id')
            ->whereBetween('sm.movement_date', [$start.' 00:00:00', $end.' 23:59:59'])
            ->groupBy('sm.variant_id','p.name','u.unit_name')
            ->select(
                'sm.variant_id',
                'p.name as product_name',
                'u.unit_name as unit_name',
                DB::raw("SUM(CASE WHEN sm.movement_type IN ('IN','OPENING') THEN sm.qty ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN sm.movement_type = 'OUT' THEN sm.qty ELSE 0 END) as total_out"),
                DB::raw("MAX(sm.balance_after) as closing_stock")
            )
            ->orderBy('p.name')
            ->get();
    }
}
