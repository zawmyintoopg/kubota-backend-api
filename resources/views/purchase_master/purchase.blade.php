@extends('layouts.master')

@section('title','purchase List')

@section('content')
<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="fas fa-file-invoice"></i> Purchase List
        </h4>
        <a href="{{ route('purchases_master.purchase_entry') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New purchase
        </a>
    </div>

    <!-- ================= FILTER & SEARCH ================= -->
    <form method="GET" class="card mb-3">
        <div class="card-body row g-2 align-items-end">

            <div class="col-md-3">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control">
                    <option value="">All Supplier</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Payment Type</label>
                <select name="payment_type_id" class="form-control">
                    <option value="">All Payments</option>
                    @foreach($paymentMethods as $p)
                        <option value="{{ $p->id }}" {{ request('payment_type_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->payment_short_code }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label>Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>

            <div class="col-md-2">
                <label>Search</label>
                <input type="text" name="search" class="form-control" placeholder="Voucher / Supplier" value="{{ request('search') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-success flex-fill" type="submit">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('purchases_master.purchases') }}" class="btn btn-secondary flex-fill">
                    <i class="fas fa-eraser"></i> Clear
                </a>
            </div>

        </div>
    </form>

    <!-- ================= TABLE ================= -->
    <div class="card">
        <div class="card-body table-responsive p-0">

            <table class="table table-bordered table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Voucher</th>
                        <th>Supplier</th>
                        <th>Payment</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchase as $row)
                        <tr>
                            <td>{{ $loop->iteration + ($purchase->currentPage()-1)*$purchase->perPage() }}</td>
                            <td>{{ date('d-m-Y', strtotime($row->purchase_date)) }}</td>
                            <td>{{ $row->voucher_no }}</td>
                            <td>{{ $row->supplier_name }}</td>
                            <td>{{ $row->payment_short_code }}</td>
                            <td class="text-end">{{ number_format($row->grand_total,2) }}</td>
                            <td>
                                @if($row->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($row->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">Draft</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('purchases_master.print',$row->id) }}" class="btn btn-info btn-sm" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>

                                @if($row->status !== 'cancelled')
                                    <a href="{{ route('purchases_master.edit',$row->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-danger btn-sm" onclick="return confirm('Cancel this purchase?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No purchases found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <!-- ================= PAGINATION ================= -->
    <div class="mt-3">
        {{ $purchase->links() }}
    </div>

</div>
@endsection
