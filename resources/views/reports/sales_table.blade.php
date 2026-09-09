<div class="card">
    <div class="card-header fw-bold">🧾 Sales Details</div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Voucher</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Grand Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales ?? [] as $sale)
                <tr>
                    <td>{{ $sale->sale_date }}</td>
                    <td>{{ $sale->voucher_no }}</td>
                    <td>{{ $sale->customer->customer_name ?? 'Guest' }}</td>
                    <td>
                        @foreach($sale->saleDetails as $d)
                            {{ $d->item->item_name }} ({{ $d->quantity }})<br>
                        @endforeach
                    </td>
                    <td class="text-end">{{ number_format($sale->grand_total) }}</td>
                    <td class="text-end">{{ number_format($sale->paid_amount) }}</td>
                    <td class="text-end">{{ number_format($sale->balance_amount) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
