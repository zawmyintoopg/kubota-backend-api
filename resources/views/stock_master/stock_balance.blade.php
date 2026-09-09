@extends('layouts.master')
@section('title','Stock Dashboard')

@section('content')
<div class="container mt-3">
    <h3>Stock Dashboard</h3>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card p-3 text-center bg-primary text-white">
                <h5>Total Products</h5>
                <h3>{{ $stocks->total() }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center bg-success text-white">
                <h5>Total Stock Value</h5>
                <h3>${{ number_format($totalValue,2) }}</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 text-center bg-danger text-white">
                <h5>Low Stock Products</h5>
                <h3>{{ $lowStockCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Search and Export -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="d-flex">
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Search Product ID" value="{{ $search ?? '' }}">
            <button id="resetFilterBtn" class="btn btn-warning ms-2">Reset Filter</button>
        </div>
        <div>
            <a href="{{ route('stock.balance', ['export'=>'pdf','search'=>$search ?? '']) }}" class="btn btn-danger">Export PDF</a>
            <a href="{{ route('stock.balance', ['export'=>'excel','search'=>$search ?? '']) }}" class="btn btn-success">Export Excel</a>
            <button class="btn btn-secondary" onclick="window.print()">Print</button>
        </div>
    </div>

    <!-- Charts -->
    <div id="charts" class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 text-center">
                <h5>Total Stock Value</h5>
                <canvas id="totalValueChart" height="100"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 text-center">
                <h5>Low Stock Products</h5>
                <canvas id="lowStockChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Hidden chart data -->
    <input type="hidden" id="totalValueData" value="{{ $totalValue }}">
    <input type="hidden" id="lowStockCountData" value="{{ $lowStockCount }}">
    <input type="hidden" id="totalStocksData" value="{{ $stocks->total() }}">
    <input type="hidden" id="lowStockProductsData" value="{{ implode(',', $lowStockProducts) }}">

    <!-- Table -->
    <div id="dashboardContent">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Order Level Qty</th>
                    <th>Onhand Qty</th>
                    <th>Quantity (Base Unit)</th>
                    <th>Total Value</th>
                    <th>Reorder Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr @if($stock->onhand_qty <= $stock->orderlevel_qty) style="background-color: #f8d7da;" @endif>
                    <td>{{ $stock->variant->product_id }}</td>
                    <td>{{ $stock->orderlevel_qty }}</td>
                    <td>{{ $stock->onhand_qty }}</td>
                    <td>{{ $stock->converted_quantity }}</td>
                    <td>${{ number_format($stock->total_value,2) }}</td>
                    <td>
                        @if($stock->is_below_reorder)
                            <span class="text-danger">⚠ Low Stock</span>
                        @else
                            <span class="text-success">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {!! $stocks->links() !!}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function(){

function renderCharts(totalValue, lowStockCount, totalStocks, lowStockProducts){
    // Total Stock Value Chart
    new Chart(document.getElementById('totalValueChart'), {
        type: 'doughnut',
        data: {
            labels: ['Total Value'],
            datasets: [{ data: [totalValue], backgroundColor: ['#4e73df'], hoverOffset: 10 }]
        },
        options:{
            responsive:true,
            plugins:{ 
                legend:{ display:false },
                tooltip:{ callbacks:{ label: ()=>'Total Stock Value: $'+totalValue.toLocaleString() } }
            }
        }
    });

    // Low Stock Chart (clickable)
    new Chart(document.getElementById('lowStockChart'), {
        type: 'doughnut',
        data: {
            labels: ['Low Stock','OK'],
            datasets:[{ data: [lowStockCount,totalStocks-lowStockCount], backgroundColor:['#e74a3b','#1cc88a'], hoverOffset: 10 }]
        },
        options:{
            responsive:true,
            plugins:{
                legend:{ position:'bottom' },
                tooltip:{ callbacks:{
                    label: function(context){
                        return context.label==='Low Stock' ? 
                            'Low Stock ('+lowStockCount+'): '+lowStockProducts.join(',') :
                            'OK Stock ('+(totalStocks-lowStockCount)+')';
                    }
                }}
            },
            animation:{ animateRotate:true, animateScale:true },
            onClick: function(evt, activeEls){
                if(activeEls.length>0){
                    let index = activeEls[0].index;
                    let filter = this.data.labels[index];
                    fetchStocks($('#searchInput').val(), filter);
                }
            }
        }
    });
}

// AJAX fetch
function fetchStocks(query, filter=null, page=null){
    $.ajax({
        url:"{{ route('stock.balance') }}",
        method:'GET',
        data:{search:query, filter:filter, page:page},
        success:function(data){
            $('#dashboardContent').html(data);

            // Update charts
            let totalValue = parseFloat($('#totalValueData').val());
            let lowStockCount = parseInt($('#lowStockCountData').val());
            let totalStocks = parseInt($('#totalStocksData').val());
            let lowStockProducts = $('#lowStockProductsData').val().split(',');

            renderCharts(totalValue, lowStockCount, totalStocks, lowStockProducts);
        }
    });
}

// Events
$('#searchInput').on('keyup', ()=>fetchStocks($('#searchInput').val()));
$('#resetFilterBtn').on('click', ()=>fetchStocks(''));
$(document).on('click', '.pagination a', function(e){
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1];
    fetchStocks($('#searchInput').val(), null, page);
});

// Initial render
renderCharts(
    parseFloat($('#totalValueData').val()),
    parseInt($('#lowStockCountData').val()),
    parseInt($('#totalStocksData').val()),
    $('#lowStockProductsData').val().split(',')
);

});
</script>
@endsection
