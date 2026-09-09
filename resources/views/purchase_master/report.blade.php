@extends('layouts.master')
@section('title','Purchase Report')
@section('content')

<div class="container py-4">
    <h3 class="mb-4">Purchase Report</h3>

    <!-- Table View -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5>Purchase Table</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Voucher No</th>
                            <th>Supplier</th>
                            <th>Payment Method</th>
                            <th>Transaction Type</th>
                            <th>Grand Total</th>
                            <th>Paid Amount</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                            <td>{{ $purchase->voucher_no }}</td>
                            <td>{{ $purchase->supplier->name ?? '-' }}</td>
                            <td>{{ $purchase->paymentType->name ?? '-' }}</td>
                            <td>{{ $purchase->transactionType->name ?? '-' }}</td>
                            <td>{{ number_format($purchase->grand_total, 2) }}</td>
                            <td>{{ number_format($purchase->paid_amount, 2) }}</td>
                            <td>{{ number_format($purchase->balance_amount, 2) }}</td>
                            <td>{{ ucfirst($purchase->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Purchase by Supplier (Pie Chart)</h5>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Purchase Status Distribution (Donut Chart)</h5>
                    <canvas id="donutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    new Chart(pieCtx, {
        type: 'pie',
        data: {
            labels: @json($pieLabels),
            datasets: [{
                data: @json($pieValues),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'
                ],
            }]
        },
    });

    // Donut Chart
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: @json($donutLabels),
            datasets: [{
                data: @json($donutValues),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#f6c23e'
                ],
            }]
        },
        options: {
            cutout: '50%'
        }
    });
</script>
@endsection
