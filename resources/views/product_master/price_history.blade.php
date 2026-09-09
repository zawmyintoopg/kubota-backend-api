@extends('layouts.master')
@section('title','Price History')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">
        <i class="fas fa-clock text-warning me-2"></i>
        Product Price History
    </h5>
</div>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-mdb-dismiss="alert"></button>
</div>
@endif

{{-- FILTER --}}
<div class="card mb-3 shadow-1">
<div class="card-body p-3">
<form method="GET" class="row g-2 align-items-end">

    <div class="col-md-3">
        <label class="form-label">From Date</label>
        <input type="date" name="from"
               class="form-control form-control-sm"
               value="{{ request('from') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">To Date</label>
        <input type="date" name="to"
               class="form-control form-control-sm"
               value="{{ request('to') }}">
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary btn-sm">
            <i class="fas fa-filter"></i> Filter
        </button>

        <a href="{{ route('price.history.index') }}"
           class="btn btn-light btn-sm">
            Reset
        </a>
    </div>

</form>
</div>
</div>

{{-- TABLE --}}
<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-sm table-hover align-middle mb-0">
<thead class="table-primary text-center">
<tr>
    <th>#</th>
    <th>Product</th>
    <th>Unit</th>
    <th>Old Price</th>
    <th>New Price</th>
    <th>Changed At</th>
    <th>User</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@forelse($histories as $h)
<tr>
    <td class="text-center">{{ $loop->iteration }}</td>

    <td>
        <div class="fw-semibold">{{ $h->product_name }}</div>
        <small class="text-muted">{{ $h->product_code }}</small>
    </td>

    <td class="text-center">{{ $h->unit_name }}</td>

    <td class="text-end text-danger fw-semibold">
        {{ number_format($h->old_price) }}
    </td>

    <td class="text-end text-success fw-semibold">
        {{ number_format($h->new_price) }}
    </td>

    <td class="text-center">
        {{ \Carbon\Carbon::parse($h->changed_at)->format('d-m-Y H:i') }}
    </td>

    <td class="text-center">
        {{ $h->changed_by ?? '-' }}
    </td>

    <td class="text-center">

        {{-- ROLLBACK --}}
        <form method="POST"
              action="{{ route('price.history.rollback', $h->id) }}"
              onsubmit="return confirm('Rollback this price?')"
              class="d-inline">
            @csrf
            <button class="btn btn-danger btn-sm">
                <i class="fas fa-undo"></i>
            </button>
        </form>

    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted py-4">
        No price history found
    </td>
</tr>
@endforelse
</tbody>
</table>
</div>

@if($histories->hasPages())
<div class="card-footer">
    {{ $histories->links() }}
</div>
@endif

</div>

@endsection
