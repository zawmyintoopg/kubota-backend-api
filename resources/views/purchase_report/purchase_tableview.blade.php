@extends('layouts.app')
@section('title','Purchase Report')
@section('content')

<div class="container mt-5">
    <h3 class="mb-4">📦 Purchase Report</h3>

    <!-- FILTER FORM -->
    <form method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-auto">
            <label>Report Type</label>
            <select name="type" class="form-select">
                <option value="daily" {{ ($type=='daily')?'selected':'' }}>Daily</option>
                <option value="monthly" {{ ($type=='monthly')?'selected':'' }}>Monthly</option>
                <option value="custom" {{ ($type=='custom')?'selected':'' }}>Custom</option>
            </select>
        </div>
        <div class="col-auto">
            <label>From</label>
            <input type="date" name="from" class="form-control" value="{{ $from?->format('Y-m-d') }}">
        </div>
        <div class="col-auto">
            <label>To</label>
            <input type="date" name="to" class="form-control" value="{{ $to?->format('Y-m-d') }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

    <!-- EXPORT BUTTONS -->
    <div class="mb-3">
        <a href="{{ route('purchase.report.pdf', request()->all()) }}" class="btn btn-sm btn-danger">PDF</a>
        <a href="{{ route('purchase.report.excel', request()->all()) }}" class="btn btn-sm btn-success">Excel</a>
    </div>

    <!-- PURCHASE LIST TABLE -->
    <div class="card shadow-sm p-3 mb-4">
        <h5>📄 Purchase List</h5>
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Invoice</th>
                    <th>Supplier</th>
                    <th>Payment Type</th>
                    <th>Transaction Type</th>
                    <th>Grand Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $i => $p)
                <tr>
                    <td>{{ $i + $purchases->firstItem() }}</td>
                    <td>{{ $p->invoice_no }}</td>
                    <td>{{ $p->supplier->name ?? '-' }}</td>
                    <td>{{ $p->paymentType->name ?? '-' }}</td>
                    <td>{{ $p->transactionType->name ?? '-' }}</td>
                    <td>{{ number_format($p->grand_total,2) }}</td>
                    <td>
                        <span class="badge bg-{{ $p->status=='Paid'?'success':($p->status=='Pending'?'warning':'danger') }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">{{ $purchases->withQueryString()->links() }}</div>
    </div>

    <!-- TOP BUYING PRODUCTS TABLE -->
    <div class="card shadow-sm p-3 mb-4">
        <h5>🔥 Top Buying Products</h5>
        <div class="mb-2">
            <a href="{{ route('purchase.top-products.pdf', request()->all()) }}" class="btn btn-sm btn-danger">PDF</a>
            <a href="{{ route('purchase.top-products.excel', request()->all()) }}" class="btn btn-sm btn-success">Excel</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
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
    </div>
</div>

@endsection
