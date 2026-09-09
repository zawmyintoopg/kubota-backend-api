<div class="card shadow-2 mb-3">
    <div class="card-header fw-bold bg-white">🏆 Best Sellers (Top Selling Items)</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Sales Amount</th>
                </tr>
            </thead>
            <tbody>
                @php $rank = 1; @endphp
                @forelse($bestSellers ?? [] as $item)
                    <tr>
                        <td>{{ $rank++ }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td class="text-center">{{ $item->total_qty }}</td>
                        <td class="text-end">{{ number_format($item->total_amount) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No sales yet</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
