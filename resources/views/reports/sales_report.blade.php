@extends('layouts.master')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">📊 Sales Report</h4>

    <!-- FILTER FORM -->
    <form method="GET" action="{{ route('reports.sales') }}" class="card p-3 mb-3">
        <div class="row g-2 align-items-end">

            <!-- From Date -->
            <div class="col-md-2">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date', now()->toDateString()) }}">
            </div>

            <!-- To Date -->
            <div class="col-md-2">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->toDateString()) }}">
            </div>

            <!-- Search By -->
            <div class="col-md-2">
                <label class="form-label">Search By</label>
                <select name="search_by" id="search_by" class="form-select">
                    <option value="">-- Select --</option>
                    <option value="voucher" {{ request('search_by') == 'voucher' ? 'selected' : '' }}>Voucher</option>
                    <option value="customer" {{ request('search_by') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="payment" {{ request('search_by') == 'payment' ? 'selected' : '' }}>Payment Method</option>
                </select>
            </div>

            <!-- Dynamic Input -->
            <div class="col-md-4" id="dynamic_input"></div>

            <!-- Buttons -->
            <div class="col-md-2 d-flex flex-column gap-2">
                <button class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Search
                </button>
                <div class="d-flex gap-2 mt-1">
                    <a href="{{ route('reports.sales.excel', request()->all()) }}" class="btn btn-success w-100">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <!-- <a href="{{ route('reports.sales.pdf', request()->all()) }}" class="btn btn-danger w-100">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a> -->
                </div>
            </div>

        </div>
    </form>

    <!-- =========================
        DETAILS REPORT
    ========================== -->
    <div class="card mb-3">
        <div class="card-header fw-bold">🧾 Sales Details</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Voucher</th>
                        <th>Customer</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $detailTotalGrand = 0;
                        $detailTotalPaid = 0;
                        $detailTotalBalance = 0;
                    @endphp
                    @forelse($sales ?? [] as $sale)
                        @php
                            $detailTotalGrand += $sale->grand_total;
                            $detailTotalPaid += $sale->paid_amount;
                            $detailTotalBalance += $sale->balance_amount;
                        @endphp
                        <tr>
                            <td>{{ $sale->sale_date }}</td>
                            <td>{{ $sale->voucher_no }}</td>
                            <td>{{ $sale->customer->customer_name ?? 'Guest' }}</td>
                            <td class="text-end">{{ number_format($sale->grand_total) }}</td>
                            <td class="text-end">{{ number_format($sale->paid_amount) }}</td>
                            <td class="text-end">{{ number_format($sale->balance_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No records found</td>
                        </tr>
                    @endforelse

                    <!-- Total Row -->
                    @if(count($sales))
                    <tr class="fw-bold table-light">
                        <td colspan="3" class="text-end">Total</td>
                        <td class="text-end">{{ number_format($detailTotalGrand) }}</td>
                        <td class="text-end">{{ number_format($detailTotalPaid) }}</td>
                        <td class="text-end">{{ number_format($detailTotalBalance) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- =========================
        SUMMARY REPORT
    ========================== -->
    <div class="card">
        <div class="card-header fw-bold">📈 Sales Summary (Payment Method + Date)</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Payment Method</th>
                        <th>Date</th>
                        <th class="text-center">Transaction Count</th>
                        <th class="text-end">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTransactions = 0;
                        $totalAmount = 0;
                    @endphp
                    @forelse($summary ?? [] as $row)
                        @php
                            $totalTransactions += $row->transaction_count;
                            $totalAmount += $row->total_amount;
                        @endphp
                        <tr>
                            <td>{{ $row->payment_method_name }}</td>
                            <td>{{ $row->sale_day }}</td>
                            <td class="text-center">{{ $row->transaction_count }}</td>
                            <td class="text-end">{{ number_format($row->total_amount) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No records found</td>
                        </tr>
                    @endforelse

                    <!-- Total Row -->
                    @if(count($summary))
                    <tr class="fw-bold table-light">
                        <td colspan="2" class="text-end">Total</td>
                        <td class="text-center">{{ $totalTransactions }}</td>
                        <td class="text-end">{{ number_format($totalAmount) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- =========================
    Dynamic Input Switching
========================== -->
<script>
const dynamicInput = document.getElementById('dynamic_input');
const searchBySelect = document.getElementById('search_by');

const oldSearchBy = "{{ request('search_by') }}";
const oldVoucher = "{{ request('voucher') }}";
const oldCustomer = "{{ request('customer_id') }}";
const oldPayment = @json(request('payment_method') ?? []);

const customers = @json($customers ?? []);
const paymentMethods = @json($paymentMethods ?? []);

function renderInput(type){
    let html = '';
    if(type === 'voucher'){
        html = `<label class="form-label">Voucher No</label>
                <input type="text" name="voucher" class="form-control" value="${oldVoucher}">`;
    } else if(type === 'customer'){
        html = `<label class="form-label">Customer</label>
                <select name="customer_id" class="form-select">
                    <option value="">-- All Customers --</option>
                    ${customers.map(c => `<option value="${c.id}" ${c.id == oldCustomer ? 'selected' : ''}>${c.customer_name}</option>`).join('')}
                </select>`;
    } else if(type === 'payment'){
        html = `<label class="form-label">Payment Method</label>
                <select name="payment_method[]" class="form-select" multiple>
                    ${paymentMethods.map(p => `<option value="${p}" ${oldPayment.includes(p) ? 'selected' : ''}>${p}</option>`).join('')}
                </select>`;
    }
    dynamicInput.innerHTML = html;
}

// Initial render
if(oldSearchBy) renderInput(oldSearchBy);

// Change listener
searchBySelect.addEventListener('change', ()=>renderInput(searchBySelect.value));
</script>
@endsection
