@extends('layouts.master')

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

    {{-- DATE SEARCH --}}
    <form method="GET" action="{{ route('admindashboarddate') }}" class="row g-2 mb-4" id="dateForm">
            <div class="col-md-3">
                <input type="date"
                    name="date"
                    value="{{ $selectedDate ?? now()->toDateString() }}"
                    class="form-control">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                    <span id="btnText">Search</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>

            <div class="col-12 mt-2" id="loadingMessage" style="display: none;">
                <div class="alert alert-info p-2 m-0">Loading data, please wait...</div>
            </div>
        </form>


    <!-- SUMMARY BOXES -->
    <div class="row g-3 align-items-stretch mb-4">

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-2 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Today Purchase</small>
                        <h4 class="mb-0">
                            MMK {{ number_format($totalPurchase ?? 0) }}
                        </h4>
                    </div>
                    <i class="fas fa-cash-register fa-2x text-info"></i>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-2 h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Sales</small>
                        <h4 class="mb-0">
                            MMK {{ number_format($totalSales ?? 0) }}
                        </h4>
                    </div>
                    <i class="fas fa-chart-line fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    

  


    </div>

    <!-- CHARTS -->
    <div class="row g-3">

        <div class="col-12">
            <div class="card shadow-2">
                <div class="card-header bg-white fw-bold">
                    Purchases (Selected Date Range)
                </div>
                <div class="card-body">
                    <canvas id="purchaseChart" height="90"></canvas>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById('dateForm').addEventListener('submit', function() {
    // Show spinner
    document.getElementById('btnSpinner').classList.remove('d-none');
    // Hide button text
    document.getElementById('btnText').textContent = 'Loading...';
    // Show loading message
    document.getElementById('loadingMessage').style.display = 'block';
});


</script>
@endpush
