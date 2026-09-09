<?php

namespace App\Exports;

use App\Models\Purchase;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class PurchasesExport implements FromView
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $type = $this->request->type ?? 'daily';
        $from = $this->request->from ? \Carbon\Carbon::parse($this->request->from) : null;
        $to   = $this->request->to ? \Carbon\Carbon::parse($this->request->to) : null;
        $tab  = $this->request->tab ?? 'summary';

        $purchases = Purchase::with(['supplier','paymentType'])
            ->when($type=='daily' && !$from, fn($q)=>$q->whereDate('purchase_date', today()))
            ->when($type=='monthly' && !$from, fn($q)=>$q->whereMonth('purchase_date', now()->month)->whereYear('purchase_date', now()->year))
            ->when($from && $to, fn($q)=>$q->whereBetween('purchase_date', [$from->startOfDay(), $to->endOfDay()]))
            ->orderBy('purchase_date','desc')
            ->get();

        return view('purchase_report.excel', compact('purchases','tab'));
    }
}
