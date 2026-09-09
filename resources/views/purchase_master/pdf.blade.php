<h3>Purchase {{ $purchase->voucher_no }}</h3>
<p>Supplier : {{ $purchase->supplier->supplier_name }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
<tr>
<th>#</th><th>Product</th><th>Unit</th><th>Qty</th><th>Buy</th><th>Total</th>
</tr>
@foreach($purchase->items as $i=>$it)
<tr>
<td>{{ $i+1 }}</td>
<td>{{ $it->product->name }}</td>
<td>{{ $it->variant->unit->unit_name }}</td>
<td>{{ $it->qty }}</td>
<td>{{ $it->buyprice }}</td>
<td>{{ $it->total_amount }}</td>
</tr>
@endforeach
</table>

<h4 align="right">Grand Total : {{ $purchase->grand_total }}</h4>
