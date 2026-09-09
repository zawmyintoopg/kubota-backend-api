@extends('layouts.master')
@section('title','Stock Movement Report')

@section('content')
<div class="container-fluid">

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
        <select name="type" class="form-select">
            <option value="daily" {{ $type=='daily'?'selected':'' }}>Daily</option>
            <option value="monthly" {{ $type=='monthly'?'selected':'' }}>Monthly</option>
        </select>
    </div>

    <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="{{ $date }}">
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary">Filter</button>
    </div>

    <div class="col-md-4 text-end">
        <a href="{{ route('stock-report.export', ['type'=>$type,'date'=>$date,'format'=>'csv']) }}" class="btn btn-success">Export CSV</a>
        <a href="{{ route('stock-report.export', ['type'=>$type,'date'=>$date,'format'=>'pdf']) }}" class="btn btn-danger">Export PDF</a>
    </div>
</form>

<div class="card shadow-sm">
<div class="card-body table-responsive">
<table class="table table-bordered table-sm text-center">
<thead class="table-dark">
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
    <td>{{ $r->product_name }}</td>
    <td>{{ $r->unit_name ?? '-' }}</td>
    <td>{{ $r->variant_id }}</td> {{-- You can replace with variant name if you store it --}}
    <td>0</td> {{-- Replace with real opening stock if available --}}
    <td>{{ number_format($r->total_in, 2) }}</td>
    <td>{{ number_format($r->total_out, 2) }}</td>
    <td class="{{ $r->closing_stock < 0 ? 'text-danger' : '' }}">
        {{ number_format($r->closing_stock, 2) }}
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

</div>
@endsection
