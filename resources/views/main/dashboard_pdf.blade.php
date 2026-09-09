<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h3,h4 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 6px; text-align: center; }
        .chart-img { display: block; margin: 10px auto; max-width: 600px; }
    </style>
</head>
<body>

<h3>Dashboard Report - {{ now()->toDateString() }}</h3>

<h4>⚠️ Low Stock Products</h4>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Onhand Qty</th>
            <th>Minimum Stock</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 1; @endphp
        @forelse($lowStockProducts as $stock)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $stock->product_name }}</td>
                <td>{{ $stock->onhand_qty }}</td>
                <td>{{ $stock->min_qty ?? 5 }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4">No low stock products</td>
            </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
