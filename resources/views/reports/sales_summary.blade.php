<div class="card">
    <div class="card-header fw-bold">📈 Sales Summary (Daily)</div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Bills</th>
                    <th>Sub Total</th>
                    <th>Discount</th>
                    <th>Tax</th>
                    <th>Grand Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary ?? [] as $row)
                <tr>
                    <td>{{ $row->sale_day }}</td>
                    <td class="text-center">{{ $row->total_bills }}</td>
                    <td class="text-end">{{ number_format($row->sub_total) }}</td>
                    <td class="text-end">{{ number_format($row->discount) }}</td>
                    <td class="text-end">{{ number_format($row->tax) }}</td>
                    <td class="text-end fw-bold">{{ number_format($row->grand_total) }}</td>
                    <td class="text-end">{{ number_format($row->paid) }}</td>
                    <td class="text-end">{{ number_format($row->balance) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
