<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stock Movement Report</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: center; }
    th { background-color: #f0f0f0; }
    .text-left { text-align: left; }
    .text-danger { color: red; }
</style>
</head>
<body>

<h3 style="text-align:center;">Stock Movement Report</h3>
<p><strong>Type:</strong> {{ ucfirst($type) }} | <strong>Date:</strong> {{ $date }}</p>

<table>
    <thead>
        <tr>
            <th>Product</th>
            <th>Unit</th>
            <th>Variant</th>
            <th>Opening</th>
            <th>IN</th>
            <th>OUT</th>
            <th>Closing</th>
        </tr>
    </thead>
    <tbody>
    @foreach($report as $r)
        <tr>
            <td class="text-left">{{ $r->product_name }}</td>
            <td>{{ $r->unit_name ?? '-' }}</td>
            <td>{{ $r->variant_id }}</td> {{-- Replace with variant_name if available --}}
            <td>0</td> {{-- Replace with real opening stock if available --}}
            <td>{{ number_format($r->total_in,2) }}</td>
            <td>{{ number_format($r->total_out,2) }}</td>
            <td class="{{ $r->closing_stock < 0 ? 'text-danger' : '' }}">
                {{ number_format($r->closing_stock,2) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
