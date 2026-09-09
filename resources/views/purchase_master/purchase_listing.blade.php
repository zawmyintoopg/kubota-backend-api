@extends('layouts.master')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid">

<div class="d-flex justify-content-between mb-3">
    <h4>Sales List - {{ now()->format('d M Y') }}</h4> 
    <a href="{{ route('purchaseCreate') }}" class="btn btn-primary">
        + New Sale
    </a>
</div>

<table class="table table-sm table-striped">
<thead class="table-dark">
<tr>
    <th>#</th>
    <th>Voucher</th>
    <th>Date</th>
    <th>Supplier</th>
    <th>Payment</th>
    <th>Total</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
@foreach($purchase as $s)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $s->voucher_no }}</td>
    <td>{{ \Carbon\Carbon::parse($s->purchase_date)->format('d-m-Y H:i') }}</td>
    <td>{{ $s->supplier_name ?? 'Guest' }}</td>
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
    {{ $purchase->links() }}
</div>

</div>
@endsection
