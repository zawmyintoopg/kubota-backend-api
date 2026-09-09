<h4>Sales Report</h4>

<!-- DETAILS -->
<h5>Details Report</h5>
<table border="1" width="100%" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Date</th>
            <th>Voucher</th>
            <th>Customer</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sales as $sale)
        <tr>
            <td>{{ $sale->sale_date }}</td>
            <td>{{ $sale->voucher_no }}</td>
            <td>{{ $sale->customer->customer_name ?? 'Guest' }}</td>
            <td>{{ number_format($sale->grand_total) }}</td>
            <td>{{ number_format($sale->paid_amount) }}</td>
            <td>{{ number_format($sale->balance_amount) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" align="center">No records found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<!-- SUMMARY -->
<h5>Summary Report</h5>
<table border="1" width="100%" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Payment Method</th>
            <th>Date</th>
            <th>Transaction Count</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalTransactions = 0;
            $totalAmount = 0;
        @endphp
        @foreach($summary as $row)
            @php
                $totalTransactions += $row->transaction_count;
                $totalAmount += $row->total_amount;
            @endphp
            <tr>
                <td>{{ $row->payment_method_name }}</td>
                <td>{{ $row->sale_day }}</td>
                <td>{{ $row->transaction_count }}</td>
                <td>{{ number_format($row->total_amount) }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2" align="right"><strong>Total</strong></td>
            <td><strong>{{ $totalTransactions }}</strong></td>
            <td><strong>{{ number_format($totalAmount) }}</strong></td>
        </tr>
    </tbody>
</table>
