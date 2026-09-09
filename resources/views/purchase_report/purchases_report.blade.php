@extends('layouts.app')
@section('title','Purchase Report')
@section('content')

<div class="container mt-5">
    <h3>📦 Purchase Report</h3>

    <!-- ================= FILTER ================= -->
    <form id="reportForm" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>Report Type</label>
            <select id="reportType" name="type" class="form-select">
                <option value="">Select</option>
                <option value="daily">Daily</option>
                <option value="monthly">Monthly</option>
                <option value="custom">Custom</option>
            </select>
        </div>

        <!-- Daily Date -->
        <div class="col-md-3 d-none" id="dailyDiv">
            <label>Date</label>
            <input type="date" name="date" class="form-control">
        </div>

        <!-- Monthly -->
        <div class="col-md-3 d-none" id="monthlyDiv">
            <label>Month</label>
            <input type="month" name="month" class="form-control">
        </div>

        <!-- Custom -->
        <div class="col-md-3 d-none" id="customDiv">
            <label>From</label>
            <input type="date" name="from" class="form-control">
        </div>
        <div class="col-md-3 d-none" id="customDivTo">
            <label>To</label>
            <input type="date" name="to" class="form-control">
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">🔍 Search</button>
        </div>
    </form>

    <!-- ================= LOADING SPINNER ================= -->
    <div id="loading" class="text-center my-5 d-none">
        <div class="spinner-border text-primary" style="width:5rem; height:5rem;"></div>
        <p>Loading data...</p>
    </div>

    <!-- ================= DATA TABLE ================= -->
    <div id="dataDiv" class="d-none">
        <div class="mb-3">
            <a id="pdfExport" href="#" class="btn btn-sm btn-danger">PDF</a>
            <a id="excelExport" href="#" class="btn btn-sm btn-success">Excel</a>
        </div>

        <table class="table table-bordered table-striped">
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
            <tbody id="purchaseTableBody">
                <!-- Filled dynamically -->
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination" id="pagination"></ul>
        </nav>

        <!-- ================= PIE CHART ================= -->
        <div class="mt-5">
            <h5>Purchase by Supplier</h5>
            <canvas id="supplierChart"></canvas>
            <button id="exportChartPdf" class="btn btn-danger mt-2">Export Chart PDF</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const reportType = document.getElementById('reportType');
    const dailyDiv = document.getElementById('dailyDiv');
    const monthlyDiv = document.getElementById('monthlyDiv');
    const customDiv = document.getElementById('customDiv');
    const customDivTo = document.getElementById('customDivTo');

    reportType.addEventListener('change', function(){
        dailyDiv.classList.add('d-none');
        monthlyDiv.classList.add('d-none');
        customDiv.classList.add('d-none');
        customDivTo.classList.add('d-none');

        if(this.value == 'daily') dailyDiv.classList.remove('d-none');
        if(this.value == 'monthly') monthlyDiv.classList.remove('d-none');
        if(this.value == 'custom') {
            customDiv.classList.remove('d-none');
            customDivTo.classList.remove('d-none');
        }
    });

    const form = document.getElementById('reportForm');
    const loading = document.getElementById('loading');
    const dataDiv = document.getElementById('dataDiv');
    const tableBody = document.getElementById('purchaseTableBody');

    let supplierChart;

    form.addEventListener('submit', function(e){
        e.preventDefault();
        dataDiv.classList.add('d-none');
        loading.classList.remove('d-none');

        const formData = new FormData(form);
        fetch("{{ route('purchase.report.detail') }}?" + new URLSearchParams(formData))
        .then(res => res.json())
        .then(data => {
            loading.classList.add('d-none');
            dataDiv.classList.remove('d-none');

            // Fill table
            tableBody.innerHTML = '';
            data.purchases.data.forEach((p,i)=>{
                tableBody.innerHTML += `<tr>
                    <td>${i+1}</td>
                    <td>${p.invoice_no}</td>
                    <td>${p.supplier}</td>
                    <td>${p.payment_type}</td>
                    <td>${p.transaction_type}</td>
                    <td>${p.grand_total}</td>
                    <td>${p.status}</td>
                    <td>${p.purchase_date}</td>
                </tr>`;
            });

            // Pie chart
            const ctx = document.getElementById('supplierChart').getContext('2d');
            if(supplierChart) supplierChart.destroy();
            supplierChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.supplierLabels,
                    datasets:[{
                        data: data.supplierTotals,
                        backgroundColor:['#007bff','#28a745','#ffc107','#dc3545','#6c757d','#17a2b8']
                    }]
                }
            });

            // Set export links
            document.getElementById('pdfExport').href = data.pdf_url;
            document.getElementById('excelExport').href = data.excel_url;
        });
    });
});
</script>
@endpush
