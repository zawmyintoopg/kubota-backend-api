<!DOCTYPE html>
<html>
<head>
<title>Purchase Invoice</title>
<style>
@page{
    size: A4;
    margin: 15mm;
}
body{
    font-family: Arial;
    font-size: 13px;
}
table{
    width:100%;
    border-collapse:collapse;
}
th,td{
    border:1px solid #000;
    padding:6px;
}
.text-end{text-align:right}
.text-center{text-align:center}
</style>
</head>
<body onload="window.print()">

<h2 class="text-center">CITY MART</h2>
<p>
<b>Voucher:</b> {{ $purchase->voucher_no }}<br>
<b>Date:</b> {{ $purchase->purchase_date }}<br>
<b>Supplier:</b> {{ $purchase->supplier->name }}
</p>

<table>
<thead>
<tr>
<th>#</th>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>
</thead>
<tbody>
@foreach($purchase->items as $i)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $i->product->name }}</td>
<td class="text-center">{{ $i->qty }}</td>
<td class="text-end">{{ $i->buy_price }}</td>
<td class="text-end">{{ $i->total_amount }}</td>
</tr>
@endforeach
</tbody>
</table>

<br>

<table>
<tr><td class="text-end">Sub Total</td><td class="text-end">{{ $purchase->sub_total }}</td></tr>
<tr><td class="text-end">Discount</td><td class="text-end">{{ $purchase->discount_amount }}</td></tr>
<tr><td class="text-end">Tax</td><td class="text-end">{{ $purchase->tax_amount }}</td></tr>
<tr><td class="text-end"><b>Grand Total</b></td><td class="text-end"><b>{{ $purchase->grand_total }}</b></td></tr>
</table>

</body>
</html>
