@extends('layouts.master') {{-- your master layout --}}

@section('title','Dashboard')

@section('content')

<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Dashboard</h5>
        <span class="badge bg-warning text-dark">
            {{ auth()->user()->role }}
        </span>
    </div>
    @session('authMessage')
        <p style="color: red">{{ session('authMessage') }} account</p>
   @endsession
    <!-- SUMMARY BOXES -->
    <div class="row g-3 align-items-stretch mb-4">


        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-2 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Today Sales</small>
                        <h4 class="mb-0">MMK 1,250,000</h4>
                    </div>
                    <i class="fas fa-cash-register fa-2x text-info"></i>
                </div>
            </div>
        </div>

       

    </div>

    <!-- CHARTS -->
    <div class="row g-3">

        <!-- SALES LINE -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-2 h-100">
                <div class="card-header bg-white fw-bold">
                    Monthly Sales
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="120"></canvas>
                </div>
            </div>
        </div>

        

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* SALES */
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
            label: 'Sales (MMK)',
            data: [1200000,1500000,1100000,1800000,2000000,2300000],
            borderWidth: 3,
            tension: .4
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});

/* PROFIT */
new Chart(document.getElementById('profitChart'), {
    type: 'doughnut',
    data: {
        labels: ['Product A','Product B','Product C'],
        datasets: [{
            data: [45,30,25]
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

/* PURCHASE */
new Chart(document.getElementById('purchaseChart'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun'],
        datasets: [{
            label: 'Purchases',
            data: [800000,900000,700000,1000000,1200000,1500000]
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
