<!DOCTYPE html>
<html>
<head>
    <title>Purchase Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 6px; text-align: center; }
        h4 { margin-bottom: 10px; }
        .chart { width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body>

<h3>Purchase Report</h3>

@if($tab=='summary')
<h4>Summary Table</h4>
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

<h4>Pie Chart: Purchase by Supplier</h4>
<div class="chart">
    <canvas id="pieChart" width="400" height="200"></canvas>
</div>

<h4>Donut Chart: Payment Type Distribution</h4>
<div class="chart">
    <canvas id="donutChart" width="400" height="200"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var ctxPie = document.getElementById('pieChart').getContext('2d');
new Chart(ctxPie, {
    type: 'pie',
    data: {
        labels: {!! json_encode($pieLabels) !!},
        datasets: [{
            data: {!! json_encode($pieValues) !!},
            backgroundColor: ['#FF6384','#36A2EB','#FFCE56','#4BC0C0','#9966FF','#FF9F40']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

var ctxDonut = document.getElementById('donutChart').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($donutLabels) !!},
        datasets: [{
            data: {!! json_encode($donutValues) !!},
            backgroundColor: ['#36A2EB','#FF6384','#FFCE56','#4BC0C0','#9966FF','#FF9F40']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

@else
<h4>Detail Table</h4>
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

</body>
</html>
    