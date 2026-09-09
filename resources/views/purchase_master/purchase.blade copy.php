@extends('layouts.master11')

@section('title','Purchase List')

@section('content')

<!-- ================= HEADER + BUTTONS ================= -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <h5 class="blue-text text-darken-2"><i class="fas fa-cart-plus"></i> Purchase List</h5>

    <div class="d-flex align-items-center flex-wrap gap-2">
        <!-- Tablet Mode Toggle -->
        <button id="tabletModeBtn" class="btn-small waves-effect waves-light blue">
            <i class="fas fa-tablet-alt"></i> Exit Tablet Mode
        </button>

        <!-- New Purchase -->
        <a href="{{ route('purchase_master.purchase_entry') }}" class="btn waves-effect waves-light green">
            <i class="fas fa-plus"></i> New Purchase
        </a>
    </div>
</div>

<!-- ================= FILTERS ================= -->
<form method="GET" class="row mb-3">
    <div class="col s12 m3">
        <label for="from_date">From Date</label>
        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="browser-default">
    </div>
    <div class="col s12 m3">
        <label for="to_date">To Date</label>
        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="browser-default">
    </div>
    <div class="col s12 m3">
        <label for="supplier">Supplier</label>
        <select name="supplier" id="supplier" class="browser-default">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $s)
                <option value="{{ $s->id }}" {{ request('supplier')==$s->id?'selected':'' }}>{{ $s->supplier_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col s12 m3">
        <label for="payment">Payment Method</label>
        <select name="payment" id="payment" class="browser-default">
            <option value="">All Payment</option>
            @foreach($paymentMethods as $p)
                <option value="{{ $p->id }}" {{ request('payment')==$p->id?'selected':'' }}>{{ $p->payment_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col s12 mt-2">
        <button type="submit" class="btn waves-effect waves-light blue"><i class="fas fa-filter"></i> Filter</button>
        <a href="{{ route('purchase_master.purchase') }}" class="btn waves-effect waves-light grey">Reset</a>
    </div>
</form>

<!-- ================= SUCCESS MESSAGE ================= -->
@if(session('success'))
<div class="card-panel green lighten-4 green-text text-darken-4">
    {{ session('success') }}
</div>
@endif

<!-- ================= TABLE ================= -->
<div id="tabletWrapper" class="tablet-container">
    <div class="card white z-depth-2 table-container">
        <div class="card-content p-2 table-responsive">
            <table class="highlight centered responsive-table">
                <thead class="blue lighten-3">
                    <tr>
                        <th class="sticky-col">#</th>
                        <th class="sticky-col2">Date</th>
                        <th>Voucher No</th>
                        <th>Supplier</th>
                        <th>Payment</th>
                        <th>Transaction</th>
                        <th>Grand Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($purchases as $index => $p)
                    <tr>
                        <td class="sticky-col">{{ $purchases->firstItem() + $index }}</td>
                        <td class="sticky-col2">{{ \Carbon\Carbon::parse($p->purchase_date)->format('d-m-Y') }}</td>
                        <td class="blue-text text-darken-2">{{ $p->voucher_no }}</td>
                        <td>{{ $p->supplier_name }}</td>
                        <td>{{ $p->payment_short_code }}</td>
                        <td>{{ $p->transaction_name }}</td>
                        <td class="right-align">{{ number_format($p->grand_total ?? 0, 2) }}</td>
                        <td class="right-align">{{ number_format($p->paid_amount ?? 0, 2) }}</td>
                        <td class="right-align">{{ number_format($p->balance_amount ?? 0, 2) }}</td>
                        <td>
                            @if($p->status==='completed')
                                <span class="new badge green" data-badge-caption="">Completed</span>
                            @elseif($p->status==='cancelled')
                                <span class="new badge red" data-badge-caption="">Cancelled</span>
                            @else
                                <span class="new badge grey" data-badge-caption="">Draft</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn-small blue" href="{{ route('purchase_master.edit',$p->id) }}"><i class="fas fa-edit"></i></a>
                            <a class="btn-small green" target="_blank" href="{{ route('purchase_master.print',$p->id) }}"><i class="fas fa-print"></i></a>
                            <a class="btn-small grey" target="_blank" href="{{ route('purchase_master.pdf',$p->id) }}"><i class="fas fa-file-pdf"></i></a>
                            <form action="{{ route('purchase_master.destroy',$p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this purchase?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-small red"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="center-align">No purchases found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="card-action">
                   </div>
    </div>
</div>

<!-- ================= STYLES ================= -->
<style>
.tablet-container {
    max-width: 1024px;
    min-height: 768px;
    margin: 20px auto;
    padding: 15px;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    overflow-x: auto;
}
.table-container table th, table td { padding: 0.6rem 0.8rem; font-size: 0.9rem; }
.sticky-col { position: sticky; left: 0; background: #f9f9f9; z-index:2; }
.sticky-col2 { position: sticky; left:50px; background: #f9f9f9; z-index:1; }
.table-container table tr:hover { background-color: #f3f6ff; }
@media(max-width:1024px){ .tablet-container { width: 100%; min-height:auto; margin:5px; padding:10px; } }
</style>

<!-- ================= TABLET MODE JS ================= -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    const tabletBtn = document.getElementById('tabletModeBtn');
    const wrapper = document.getElementById('tabletWrapper');

    // Load saved tablet preference
    if(localStorage.getItem('tabletMode')==='disabled'){
        wrapper.classList.remove('tablet-container');
        tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Enable Tablet Mode';
        tabletBtn.classList.remove('btn-primary');
        tabletBtn.classList.add('btn-outline-secondary');
    }

    tabletBtn.addEventListener('click', function(){
        if(wrapper.classList.contains('tablet-container')){
            wrapper.classList.remove('tablet-container');
            tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Enable Tablet Mode';
            tabletBtn.classList.remove('btn-primary');
            tabletBtn.classList.add('btn-outline-secondary');
            localStorage.setItem('tabletMode','disabled');
        } else {
            wrapper.classList.add('tablet-container');
            tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Exit Tablet Mode';
            tabletBtn.classList.add('btn-primary');
            tabletBtn.classList.remove('btn-outline-secondary');
            localStorage.setItem('tabletMode','enabled');
        }
    });
});
</script>

@endsection
