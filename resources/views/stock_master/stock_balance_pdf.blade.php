<h3>Stock Balance</h3>
<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Order Level Qty</th>
            <th>Onhand Qty</th>
            <th>Quantity (Base Unit)</th>
            <th>Total Value</th>
            <th>Reorder Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stocks as $stock)
        <tr @if($stock->onhand_qty <= $stock->orderlevel_qty) style="background-color: #f8d7da;" @endif>
            <td>{{ $stock->variant->product_id }}</td>
            <td>{{ $stock->orderlevel_qty }}</td>
            <td>{{ $stock->onhand_qty }}</td>
            <td>{{ $stock->converted_quantity }}</td>
            <td>${{ number_format($stock->total_value,2) }}</td>
            <td>
                @if($stock->is_below_reorder)
                    ⚠ Low Stock
                @else
                    OK
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
