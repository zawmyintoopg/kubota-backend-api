@extends('layouts.master')

@section('title','Dashboard')

@section('content')
<div class="container-fluid" id="mainContainer" style="padding-top: 80px;">

    <!-- SUMMARY BOXES -->
    <div class="row g-3 align-items-stretch mb-4 summary-boxes">
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card shadow-2 h-100 border-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Today Purchase</small>
                        <h4 class="mb-0" id="totalPurchase">MMK {{ number_format($totalPurchase ?? 0) }}</h4>
                    </div>
                    <i class="fas fa-cart-arrow-down fa-2x text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
            <div class="card shadow-2 h-100 border-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Sales</small>
                        <h4 class="mb-0" id="totalSales">MMK {{ number_format($totalSales ?? 0) }}</h4>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">📈 Transactions Over Time</div>
                <div class="card-body">
                    <canvas id="transactionChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">💳 Sales by Payment</div>
                <div class="card-body">
                    <canvas id="donutChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">👥 Transactions by Customer</div>
                <div class="card-body">
                    <canvas id="pieChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- BEST SELLER TABLE + CHART -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">🏆 Best Seller Table</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Quantity Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @forelse($bestSellers ?? [] as $item)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td class="text-center">{{ $item->total_qty }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No data</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">🏆 Best Seller Chart</div>
                <div class="card-body">
                    <canvas id="bestSellerChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- LOW STOCK ALERT -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-2">
                <div class="card-header fw-bold bg-white">⚠️ Low Stock Products</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Onhand Qty</th>
                                <th>Minimum Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @forelse($lowStockProducts ?? [] as $stock)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $stock->product_name }}</td>
                                <td class="text-center">{{ $stock->onhand_qty }}</td>
                                <td class="text-center">{{ $stock->min_qty ?? 5 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">All products sufficiently stocked</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- EXPORT PDF BUTTON -->
    <!-- <button id="exportPdf" class="btn btn-danger mb-3">📄 Export PDF</button> -->

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart
    const transactionChartData = @json($chartData ?? ['labels'=>[], 'sales'=>[], 'purchases'=>[]]);
    const ctxLine = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(ctxLine, {
        type:'line',
        data:{
            labels: transactionChartData.labels,
            datasets:[
                { label:'Sales', data:transactionChartData.sales, borderColor:'#198754', backgroundColor:'rgba(25,135,84,0.1)', tension:0.3 },
                { label:'Purchases', data:transactionChartData.purchases, borderColor:'#0d6efd', backgroundColor:'rgba(13,110,253,0.1)', tension:0.3 }
            ]
        },
        options:{responsive:true, maintainAspectRatio:false, scales:{y:{beginAtZero:true}}}
    });

    // Donut Chart
    const ctxDonut = document.getElementById('donutChart').getContext('2d');
    const donutChart = new Chart(ctxDonut,{
        type:'doughnut',
        data:{labels:@json($salesByPaymentData['labels']), datasets:[{data:@json($salesByPaymentData['amounts']), backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545','#6c757d']}]},
        options:{responsive:true, maintainAspectRatio:false}
    });

    // Pie Chart
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctxPie,{
        type:'pie',
        data:{labels:@json($transactionsByCustomerData['labels']), datasets:[{data:@json($transactionsByCustomerData['counts']), backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545','#6c757d','#6610f2','#fd7e14']}]},
        options:{responsive:true, maintainAspectRatio:false}
    });

    // Best Seller Chart
    const ctxBest = document.getElementById('bestSellerChart').getContext('2d');
    const bestSellerChart = new Chart(ctxBest,{
        type:'bar',
        data:{labels:@json($bestSellers->pluck('item_name')), datasets:[{label:'Quantity Sold', data:@json($bestSellers->pluck('total_qty')), backgroundColor:'#0d6efd'}]},
        options:{responsive:true, maintainAspectRatio:false, indexAxis:'y', scales:{x:{beginAtZero:true}}}
    });

    // Export PDF
    document.getElementById('exportPdf').addEventListener('click', function(){
        const charts = [
            document.getElementById('transactionChart'),
            document.getElementById('donutChart'),
            document.getElementById('pieChart'),
            document.getElementById('bestSellerChart')
        ];
        const chartsImages = charts.map(c=>c.toDataURL('image/png'));

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';
        form.style.display = 'none';
        const token = document.createElement('input');
        token.name = '_token'; token.value = '{{ csrf_token() }}'; form.appendChild(token);

        chartsImages.forEach(img=>{
            const input = document.createElement('input');
            input.name = 'charts[]';
            input.value = img;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });
</script>
@endpush
