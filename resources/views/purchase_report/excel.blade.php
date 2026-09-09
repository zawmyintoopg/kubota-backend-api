@if($tab=='summary')
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Supplier</th>
            <th>Payment Type</th>
            <th>Total Transactions</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $i=1; @endphp
        @foreach($purchases->groupBy('supplier_id') as $supplierId => $supplierGroup)
            @php
                $supplierName = $supplierGroup->first()->supplier->name ?? 'Unknown';
                $payments = $supplierGroup->groupBy('payment_type_id');
            @endphp
            @foreach($payments as $paymentId => $items)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $supplierName }}</td>
                    <td>{{ $items->first()->paymentType->name ?? 'Unknown' }}</td>
                    <td>{{ $items->count() }}</td>
                    <td>{{ number_format($items->sum('grand_total'),2) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
@else
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Voucher</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Payment Type</th>
            <th>Status</th>
            <th>Grand Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($purchases as $i => $item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->voucher_no }}</td>
            <td>{{ $item->supplier->name ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($item->purchase_date)->format('Y-m-d') }}</td>
            <td>{{ $item->paymentType->name ?? '-' }}</td>
            <td>{{ $item->status }}</td>
            <td>{{ number_format($item->grand_total,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
