@extends('layouts.app')
@section('title','Purchase Summary')
@section('content')

<div class="container mt-5">
    <h3>📊 Purchase Summary</h3>

    <!-- ================= FILTER FORM ================= -->
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

    <!-- ================= SUMMARY CARDS ================= -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h6>Total Purchases</h6>
                <h4>{{ number_format($totalPurchases ?? 0,2) }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h6>Top Supplier</h6>
                <h5>{{ $topSupplier->name ?? '-' }}</h5>
                <small>{{ number_format($topSupplier->total ?? 0,2) }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h6>Top Product</h6>
                <h5>{{ $topProduct->variant->product->name ?? '-' }}</h5>
                <small>{{ $topProduct->variant->name ?? '-' }} | {{ number_format($topProduct->total ?? 0,2) }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-center">
                <h6>Total Paid</h6>
                <h4>{{ number_format($totalPaid ?? 0,2) }}</h4>
            </div>
        </div>
    </div>

    <!-- ================= CHARTS ================= -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h6>Purchase by Supplier</h6>
            <canvas id="supplierChart"></canvas>
        </div>
        <div class="col-md-6">
            <h6>Payment Type Distribution</h6>
            <canvas id="paymentChart"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const supplierChart = new Chart(document.getElementById('supplierChart'), {
    type: 'pie',
    data: {
        labels: @json($supplierLabels),
        datasets: [{
            data: @json($supplierTotals),
            backgroundColor: @json($colors),
        }]
    }
});

const paymentChart = new Chart(document.getElementById('paymentChart'), {
    type: 'doughnut',
    data: {
        labels: @json($paymentLabels),
        datasets: [{
            data: @json($paymentTotals),
            backgroundColor: @json($colors),
        }]
    }
});
</script>
@endpush

@endsection
