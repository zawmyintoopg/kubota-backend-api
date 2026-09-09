<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Top Buying Products PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 12px; }
        .table th, .table td { vertical-align: middle; text-align: center; }
        .table thead { background: #212529; color: #fff; }
        h3 { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h3 class="text-center">🔥 Top Buying Products Report</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Total Purchased</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $i => $p)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $p->product->name ?? 'Unknown' }}</td>
                    <td>{{ number_format($p->total,2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p class="text-end mt-3">Generated: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }}</p>
    </div>
</body>
</html>
