@extends('layouts.master')

@section('content')
<div class="container-fluid">

<div class="d-flex justify-content-between mb-3">
    <h4>Sales List - {{ now()->format('d M Y') }}</h4> 
    <a href="{{ route('saleCreate') }}" class="btn btn-primary">
        + New Sale
    </a>
</div>

<table class="table table-sm table-striped">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Voucher</th>
    <th>Date</th>
    <th>Customer</th>
    <th>Payment</th>
    <th>Total</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($sale as $s)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $s->voucher_no }}</td>
    <td>{{ \Carbon\Carbon::parse($s->sale_date)->format('d-m-Y H:i') }}</td>
    <td>{{ $s->customer_name ?? 'Guest' }}</td>
    <td>{{ $s->payment_short_code }}</td>
    <td class="text-end">{{ number_format($s->grand_total) }}</td>
    <td>
        <span class="badge bg-{{ $s->status == 'Completed' ? 'success' : 'warning' }}">
            {{ $s->status }}
        </span>
    </td>
</tr>
@endforeach
</tbody>
</table>

<div class="mt-3">
    {{ $sale->links() }}
</div>

</div>
@endsection
