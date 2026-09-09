<h4>Sales Details Report</h4>
<p>
    {{ $from->toDateString() }} → {{ $to->toDateString() }}
</p>

@include('reports.sale_filter_form')

@foreach($sales as $sale)
<div class="card mb-3">
    <div class="card-header">
        <strong>{{ $sale->voucher_no }}</strong>
        <span class="float-end">
            {{ $sale->sale_date }}
        </span>
    </div>

    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleDetails as $d)
                <tr>
                    <td>{{ $d->item->item_name }}</td>
                    <td class="text-end">{{ $d->quantity }}</td>
                    <td class="text-end">{{ number_format($d->unit_price,2) }}</td>
                    <td class="text-end">{{ number_format($d->total_price,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card-footer text-end">
        Sub: {{ number_format($sale->sub_total,2) }} |
        Discount: {{ number_format($sale->discount_amount,2) }} |
        Tax: {{ number_format($sale->tax_amount,2) }} |
        <strong>Total: {{ number_format($sale->grand_total,2) }}</strong>
    </div>
</div>
@endforeach
