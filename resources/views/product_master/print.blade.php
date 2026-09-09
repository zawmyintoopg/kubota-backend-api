<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $purchase->voucher_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body onload="window.print(); window.close();">
    <h2>Invoice: {{ $purchase->voucher_no }}</h2>
    <p>Date: {{ $purchase->purchase_date }}</p>
    <p>Supplier: {{ $purchase->supplier->supplier_name }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th><th>Product</th><th>Unit</th><th>Qty</th><th>Price</th><th>Discount</th><th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->variant->product->name ?? '' }}</td>
                <td>{{ $item->variant->unit->unit_name ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price,2) }}</td>
                <td>{{ number_format($item->discount,2) }}</td>
                <td>{{ number_format($item->total_price,2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Sub Total: {{ number_format($purchase->sub_total,2) }}</p>
    <p>Discount: {{ number_format($purchase->discount_amount,2) }}</p>
    <p>Tax: {{ number_format($purchase->tax_amount,2) }}</p>
    <p>Grand Total: {{ number_format($purchase->grand_total,2) }}</p>
</body>
</html>
