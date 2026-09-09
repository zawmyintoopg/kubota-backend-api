@extends('layouts.master')

@section('title','Profit & Loss Report')
@section('content')
<div class="container-fluid">
    <h4 class="mb-3">📊 Profit & Loss Report</h4>

    <!-- FILTER FORM -->
    <form method="GET" action="{{ route('reports.profit_loss.data') }}" class="card p-3 mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ $from ?? now()->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ $to ?? now()->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select">
                    <option value="summary" {{ ($type ?? '')=='summary' ? 'selected':'' }}>Summary</option>
                    <option value="details" {{ ($type ?? '')=='details' ? 'selected':'' }}>Detail</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100"><i class="fas fa-search"></i> Generate Report</button>
            </div>
        </div>
    </form>

    <!-- SUMMARY REPORT -->
    @if(($type ?? 'summary')=='summary')
    <div class="card">
        <div class="card-header fw-bold">📈 Summary</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Revenue</th>
                        <th>Cost</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary ?? [] as $row)
                    <tr>
                        <td>{{ $row->sale_day }}</td>
                        <td class="text-end">{{ number_format($row->revenue) }}</td>
                        <td class="text-end">{{ number_format($row->cost) }}</td>
                        <td class="text-end fw-bold">{{ number_format($row->profit) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No records found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- DETAIL REPORT -->
    @if(($type ?? '')=='details')
    <div class="card">
        <div class="card-header fw-bold">🧾 Detail</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Voucher</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Cost Price</th>
                        <th>Total</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQty = $totalRevenue = $totalCost = $totalProfit = 0;
                    @endphp
                    @forelse($sales ?? [] as $sale)
                        @foreach($sale->saleDetails as $d)
                        @php
                            $revenue = $d->quantity * $d->unit_price;
                            $cost = $d->quantity * $d->cost_price;
                            $profit = $revenue - $cost;
                            $totalQty += $d->quantity;
                            $totalRevenue += $revenue;
                            $totalCost += $cost;
                            $totalProfit += $profit;
                        @endphp
                        <tr>
                            <td>{{ $sale->sale_date }}</td>
                            <td>{{ $sale->voucher_no }}</td>
                            <td>{{ $d->item->item_name ?? '' }}</td>
                            <td>{{ $d->quantity }}</td>
                            <td class="text-end">{{ number_format($d->unit_price) }}</td>
                            <td class="text-end">{{ number_format($d->cost_price) }}</td>
                            <td class="text-end">{{ number_format($revenue) }}</td>
                            <td class="text-end">{{ number_format($profit) }}</td>
                        </tr>
                        @endforeach
                    @empty
                    <tr><td colspan="8" class="text-center text-muted">No records found</td></tr>
                    @endforelse
                    @if($sales->count() > 0)
                    <tr class="table-secondary fw-bold">
                        <td colspan="3">Total</td>
                        <td>{{ $totalQty }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end">{{ number_format($totalRevenue) }}</td>
                        <td class="text-end">{{ number_format($totalProfit) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
