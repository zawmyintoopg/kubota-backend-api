<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StockBalanceExport implements FromCollection, WithHeadings
{
    protected $stocks;

    public function __construct($stocks)
    {
        $this->stocks = $stocks;
    }

    public function collection()
    {
        return $this->stocks->map(function($stock) {
            return [
                'product_id' => $stock->variant->product_id,
                'orderlevel_qty' => $stock->orderlevel_qty,
                'onhand_qty' => $stock->onhand_qty,
                'quantity' => $stock->converted_quantity,
                'total_value' => $stock->total_value,
                'reorder_status' => $stock->is_below_reorder ? 'Low Stock' : 'OK'
            ];
        });
    }

    public function headings(): array
    {
        return ['Product ID', 'Order Level Qty', 'Onhand Qty', 'Quantity (Base Unit)', 'Total Value', 'Reorder Status'];
    }

}
