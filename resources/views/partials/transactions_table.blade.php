<table class="table table-bordered table-striped dashboard-table">
    <thead class="table-dark">
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $t)
        <tr>
            <td>{{ $t->created_at ?? '-' }}</td>
            <td>{{ $t->type ?? '-' }}</td>
            <td>{{ $t->product_name ?? '-' }}</td>
            <td>{{ $t->quantity ?? 0 }}</td>
            <td>{{ number_format($t->unit_price ?? 0) }}</td>
            <td>{{ number_format($t->total ?? 0) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No transactions found</td></tr>
        @endforelse
    </tbody>
</table>
