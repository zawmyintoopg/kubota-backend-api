<?php

namespace App\Exports;

use App\Models\PurchaseItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TopProductsExport implements FromCollection, WithHeadings
{
    protected $request;
    public function __construct(Request $request){ $this->request = $request; }

    public function collection()
    {
        $query = PurchaseItem::with(['variant','variant.product']);
        $type = $this->request->type ?? 'daily';
        $from = $this->request->from ? Carbon::parse($this->request->from) : null;
        $to   = $this->request->to ? Carbon::parse($this->request->to) : null;

        if ($from && $to) {
            $query->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()]);
        } else {
            if ($type=='daily') $query->whereDate('created_at', now());
            if ($type=='monthly') $query->whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year);
        }

        $topProducts = $query->select('variant_id', DB::raw('SUM(total) as total'))
                             ->groupBy('variant_id')
                             ->orderByDesc('total')
                             ->get();

        return $topProducts->map(fn($p)=>[
            $p->variant->product->name ?? 'Unknown',
            $p->variant->name ?? 'Unknown',
            $p->total
        ]);
    }

    public function headings(): array{
        return ['Product','Variant','Total Purchased'];
    }
}
