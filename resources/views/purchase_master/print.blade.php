@extends('layouts.master')
@section('title','Purchase Receipt')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{ font-family:sans-serif; margin:0; padding:10px; background:#fff; }
h2{text-align:center; margin-bottom:10px;}
table{ width:100%; border-collapse:collapse; margin-top:10px; }
table, th, td{ border:1px solid #000; }
th, td{ padding:6px; text-align:left; }
tfoot td{ font-weight:bold; }
.total-row{ border-top:2px solid #000; }
</style>

<h2>Purchase Receipt</h2>
<p><strong>Voucher No:</strong> {{ $purchase->voucher_no }}</p>
<p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->timezone('Asia/Yangon')->format('d/m/Y H:i') }}</p>
<p><strong>Supplier:</strong> {{ $purchase->supplier->supplier_name }}</p>
<p><strong>Payment Type:</strong> {{ $purchase->paymentType->name ?? '' }}</p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Unit</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Discount</th>
            <th>Tax</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchase->items as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->variant->product->name ?? '' }}</td>
            <td>{{ $item->variant->unit->unit_name ?? '' }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_price,2) }}</td>
            <td>{{ number_format($item->discount,2) }}</td>
            <td>{{ number_format($item->tax ?? 0,2) }}</td>
            <td>{{ number_format($item->total_price,2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7">Grand Total</td>
            <td>{{ number_format($purchase->grand_total,2) }}</td>
        </tr>
        <tr>
            <td colspan="7">Paid Amount</td>
            <td>{{ number_format($purchase->paid_amount,2) }}</td>
        </tr>
        <tr>
            <td colspan="7">Balance</td>
            <td>{{ number_format($purchase->balance_amount,2) }}</td>
        </tr>
    </tfoot>
</table>

<script>
window.onload = function(){
    window.print();
}
</script>
@endsection
