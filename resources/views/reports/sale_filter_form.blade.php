@extends('layouts.master')

@section('title','Sales Report')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">Sales Report</h4>

    <!-- FILTER FORM -->
    <form id="sales-filter-form" class="mb-3">
        @csrf
        <div class="row g-2 align-items-end">

            <!-- Filter Type -->
            <div class="col-md-3">
                <label>Filter Type</label>
                <select name="filter_type" id="filter_type" class="form-select">
                    <option value="today">Today</option>
                    <option value="voucher">Voucher No</option>
                    <option value="payment">Payment Method</option>
                    <option value="customer">Customer</option>
                    <option value="date_range">Date Range</option>
                </select>
            </div>

            <!-- Voucher No -->
            <div class="col-md-3 d-none" id="voucher_div">
                <label>Voucher No</label>
                <input type="text" name="voucher_no" id="voucher_no" class="form-control" placeholder="Enter Voucher No">
            </div>

            <!-- Payment Method -->
            <div class="col-md-3 d-none" id="payment_div">
                <label>Payment Method</label>
                <select name="payment_methods[]" id="payment_methods" class="form-select" multiple>
                    @foreach($payment_methods as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Customer -->
            <div class="col-md-3 d-none" id="customer_div">
                <label>Customer</label>
                <input type="text" name="customer_name" id="customer_name" class="form-control" placeholder="Search Customer">
                <input type="hidden" name="customer_id" id="customer_id">
            </div>

            <!-- Date Range -->
            <div class="col-md-3 d-none" id="daterange_div">
                <label>From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control">
            </div>
            <div class="col-md-3 d-none" id="daterange_div2">
                <label>To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control">
            </div>

            <!-- Admin User Filter -->
            @if(Auth::user()->role === 'admin')
            <div class="col-md-3">
                <label>User</label>
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Search Button -->
            <div class="col-md-3">
                <button type="button" id="search-btn" class="btn btn-primary w-100">Search</button>
            </div>
        </div>
    </form>

    <!-- Export Buttons -->
    <div class="mb-3">
        <a href="#" id="export-pdf" class="btn btn-danger">Export PDF</a>
        <a href="#" id="export-excel" class="btn btn-success">Export Excel</a>
    </div>

    <!-- Report Container -->
    <div id="report-container">
        <p class="text-muted">Please select filters and click search</p>
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){

    // Toggle filter fields
    $('#filter_type').change(function(){
        let type = $(this).val();
        $('#voucher_div, #payment_div, #customer_div, #daterange_div, #daterange_div2').addClass('d-none');
        if(type === 'voucher') $('#voucher_div').removeClass('d-none');
        if(type === 'payment') $('#payment_div').removeClass('d-none');
        if(type === 'customer') $('#customer_div').removeClass('d-none');
        if(type === 'date_range') $('#daterange_div, #daterange_div2').removeClass('d-none');
    });

    // Customer autocomplete
    let customers = @json($customers->map(fn($c)=>['id'=>$c->id,'name'=>$c->customer_name]));
    $('#customer_name').on('input', function(){
        let val = $(this).val().toLowerCase();
        let match = customers.filter(c => c.name.toLowerCase().includes(val));
        if(match.length === 1){
            $('#customer_id').val(match[0].id);
        } else {
            $('#customer_id').val('');
        }
    });

    // Search Button Click
    $('#search-btn').click(function(){
        let data = $('#sales-filter-form').serialize();
        $.get("{{ route('reports.sales') }}", data, function(res){
            $('#report-container').html(res);

            // Update export buttons URLs
            $('#export-pdf').attr('href', "{{ route('reports.sales.pdf') }}?"+data);
            $('#export-excel').attr('href', "{{ route('reports.sales.excel') }}?"+data);
        });
    });

});
</script>
@endsection
