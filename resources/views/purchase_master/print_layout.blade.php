<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Purchase Invoice</title>

<style>
body{
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #000;
}
h3,h4{margin:0}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
table th, table td{
    border:1px solid #000;
    padding:5px;
}
.text-end{text-align:right}
.text-center{text-align:center}
.no-border td{border:none}
hr{margin:8px 0}
</style>
</head>

<body onload="window.print()">

<h3>Purchase Invoice</h3>
<hr>

<table class="no-border">
<tr>
<td>
    <strong>Voucher:</strong> {{ $purchase->voucher_no }} <br>
    <strong>Date:</strong> {{ $purchase->purchase_date }}
</td>
<td class="text-end">
    <strong>Supplier:</strong> {{ $purchase->supplier->supplier_name ?? '-' }} <br>
    <strong>Status:</strong> {{ $purchase->status }}
</td>
</tr>
</table>

<table>
<thead>
<tr class="text-center">
    <th>#</th>
    <th>Product</th>
    <th>Unit</th>
    <th>Qty</th>
    <th>Buy</th>
    <th>Total</th>
</tr>
</thead>

<tbody>
@foreach($purchase->items as $i => $it)
<tr>
    <td class="text-center">{{ $i+1 }}</td>
    <td>{{ $it->product->name }}</td>
    <td class="text-center">{{ $it->variant->unit->unit_name ?? '-' }}</td>
    <td class="text-center">{{ $it->qty }}</td>
    <td class="text-end">{{ number_format($it->buyprice) }}</td>
    <td class="text-end">{{ number_format($it->total_amount) }}</td>
</tr>
@endforeach
</tbody>
</table>

<table class="no-border" style="margin-top:10px">
<tr>
<td class="text-end"><strong>Sub Total:</strong></td>
<td class="text-end" width="120">{{ number_format($purchase->sub_total) }}</td>
</tr>

<tr>
<td class="text-end"><strong>Discount:</strong></td>
<td class="text-end">{{ number_format($purchase->discount_amount ?? 0) }}</td>
</tr>

<tr>
<td class="text-end"><strong>Tax:</strong></td>
<td class="text-end">{{ number_format($purchase->tax_amount ?? 0) }}</td>
</tr>

<tr>
<td class="text-end"><strong>Grand Total:</strong></td>
<td class="text-end"><strong>{{ number_format($purchase->grand_total) }}</strong></td>
</tr>

<tr>
<td class="text-end"><strong>Paid:</strong></td>
<td class="text-end">{{ number_format($purchase->paid_amount) }}</td>
</tr>

<tr>
<td class="text-end"><strong>Balance:</strong></td>
<td class="text-end">{{ number_format($purchase->balance_amount) }}</td>
</tr>
</table>

</body>
</html>
