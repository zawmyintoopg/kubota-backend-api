<div id="charts">
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card p-3 text-center">
            <h5>Total Stock Value</h5>
            <canvas id="totalValueChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3 text-center">
            <h5>Low Stock Products</h5>
            <canvas id="lowStockChart" height="100"></canvas>
        </div>
    </div>
</div>
</div>

<div id="stockTable">
<table class="table table-bordered table-striped">
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
                    <span class="text-danger">⚠ Low Stock</span>
                @else
                    <span class="text-success">OK</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {!! $stocks->links() !!}
</div>
</div>
