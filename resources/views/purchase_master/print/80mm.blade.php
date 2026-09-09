<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Purchase Receipt</title>

<style>
@page { size: 80mm auto; margin: 5mm; }
body{
    font-family: monospace;
    font-size:12px;
}
.center{text-align:center}
hr{border-top:1px dashed #000}
table{width:100%}
td{padding:2px 0}
.right{text-align:right}
</style>
</head>

<body onload="window.print(); setTimeout(()=>window.close(),500);">

<div class="center">
    <img src="{{ asset('images/logo.png') }}" width="120"><br>
    <strong>City Mart</strong><br>
    Purchase Voucher<br>
</div>

<hr>

<table>
<tr><td>Date</td><td class="right">{{ $purchase->purchase_date }}</td></tr>
<tr><td>Voucher</td><td class="right">{{ $purchase->voucher_no }}</td></tr>
<tr><td>Supplier</td><td class="right">{{ $purchase->supplier->name }}</td></tr>
</table>

<hr>

<table>
@foreach($purchase->items as $i)
<tr>
<td>{{ $i->product->name }} x{{ $i->qty }}</td>
<td class="right">{{ number_format($i->total_amount) }}</td>
</tr>
@endforeach
</table>

<hr>

<table>
<tr><td>Total</td><td class="right">{{ number_format($purchase->grand_total) }}</td></tr>
<tr><td>Paid</td><td class="right">{{ number_format($purchase->paid_amount) }}</td></tr>
<tr><td>Refund</td><td class="right">{{ number_format($purchase->refund_amount) }}</td></tr>
</table>

<hr>

<div class="center">
    Thank You 🙏
</div>

</body>
</html>
