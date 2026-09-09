<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- MDB UI KIT -->
<link href="{{ asset('assets/vendor/mdb/mdb.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('assets/vendor/fontawesome/css/all.min.css') }}" rel="stylesheet"/>
<style>
html, body {
    overflow-x: hidden;
    height: 100%;
    margin: 0;
}

/* HEADER */
.app-header{
    height:56px;
    position: fixed;
    top:0;
    left:0;
    right:0;
    z-index:1045;
    background:#0d6efd;
    color:#fff;
    padding:0 15px;
    display:flex;
    align-items:center;
    justify-content: space-between;
}

/* SEARCH */
.search-wrapper{
    flex:1;
    max-width:50%;
}
.search-input{
    border-radius:30px;
}

/* SIDEBAR MENU */
#appMenu{
    position: fixed;
    top:56px;
    left:-300px; /* hide by default */
    width:300px;
    height: calc(100% - 56px);
    background:#fff;
    box-shadow:2px 0 12px rgba(0,0,0,0.2);
    transition: left 0.3s ease;
    z-index:1050;
    padding:16px;
    overflow-y:auto;
}
#appMenu.show{
    left:0;
}

.app-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:10px;
}

.app-item{
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px;
    border-radius:8px;
    color:#212529;
    text-decoration:none;
    transition: .2s;
}
.app-item i{ font-size:20px; }
.app-item:hover{ background:#f0f0f0; }

/* MAIN CONTENT */
.main-content{
    margin-top:56px; /* header height */
    padding:15px;
    transition: margin-left 0.3s ease;
}

/* PAGE LOADER */
.page-loader{
    position:fixed;
    inset:0;
    background:rgba(255,255,255,.9);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:2000;
    display:none;
}
.page-loader.show{
    display:flex;
}

/* RESPONSIVE */
@media(max-width:1024px){ /* tablet */
    .search-wrapper{ max-width:50%; }
}
@media(max-width:768px){ /* mobile */
    #appMenu{ width:70%; }
    .search-wrapper{ max-width:70%; }
}
</style>
</head>
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body class="bg-light">

<!-- PAGE LOADER -->
<div id="pageLoader" class="page-loader">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- HEADER -->
<nav class="app-header">
    <button class="btn text-white" onclick="toggleMenu()">
        <i class="fas fa-grip fs-5"></i>
    </button>

    <div class="search-wrapper ms-3">
        <input type="text" id="masterSearchInput" class="form-control search-input form-control-md" placeholder="Search..." value="{{ request('search') }}">
    </div>

    <div class="ms-auto dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-mdb-toggle="dropdown">
            <i class="fas fa-user"></i> {{ auth()->user()->name ?? 'Guest' }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profile</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</nav>

<!-- SIDEBAR -->
<div id="appMenu">
    <div class="app-grid">
      
        @auth
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('adminPage') }}" class="app-item"><i class="fas fa-home text-primary"></i> Dashboard</a>

        <a href="{{ route('product_master.category') }}" class="app-item"><i class="fas fa-tags text-success"></i> Category</a>
        <a href="{{ route('product_master.unit') }}" class="app-item"><i class="fas fa-balance-scale text-info"></i> Unit</a>
        <a href="{{ route('product_master.brand') }}" class="app-item"><i class="fas fa-copyright text-dark"></i> Brand</a>
        <a href="{{ route('product_master.productList') }}" class="app-item"><i class="fas fa-box-open text-warning"></i> Items</a>
        <a href="{{ route('common.payment_method') }}" class="app-item"><i class="fas fa-credit-card text-success"></i> Payment Method</a>
        <a href="{{ route('common.transaction_type') }}" class="app-item"><i class="fas fa-exchange-alt text-info"></i> Transaction Type</a>
        <a href="{{ route('purchase_master.supplier') }}" class="app-item"><i class="fas fa-truck text-secondary"></i> Supplier</a>
        <a href="{{ route('purchaseCreate') }}" class="app-item"><i class="fas fa-shopping-cart text-primary"></i> Purchase</a>
        <a href="{{ route('purchasePage') }}" class="app-item d-flex align-items-center">
            <i class="fas fa-file-invoice text-primary"></i>
            <span>Purchase Receipt</span>
        </a>

        <a href="{{ route('stock-movements.index') }}" class="app-item"><i class="fas fa-boxes text-warning"></i> Stock Movement</a>
        <a href="{{ route('purchase.report') }}" class="app-item"><i class="fas fa-chart-line text-danger"></i> Reports</a>
        @endif
        @if(auth()->user()->role === 'cashier')
    <a href="{{ route('saleCreate') }}" class="app-item">
        <i class="fas fa-money-bill-wave text-primary"></i> Sale
    </a>
    <a href="{{ route('salePage') }}" class="app-item">
        <i class="fas fa-cash-register text-success"></i> Receipt
    </a>

    <!-- <a href="{{ route('shifts.index') }}" class="app-item">
        <i class="fas fa-clock text-warning"></i> Shifts
    </a> -->
    <a href="{{ route('reports.sales') }}" class="app-item">
        <i class="fas fa-chart-line text-danger"></i> Reports
    </a>
        @endif
        @endauth
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    @yield('content')
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
<script>
function toggleMenu(){
    document.getElementById('appMenu').classList.toggle('show');
}

// close sidebar if clicked outside
document.addEventListener('click', e => {
    const menu = document.getElementById('appMenu');
    if(!menu.contains(e.target) && !e.target.closest('.fa-grip')){
        menu.classList.remove('show');
    }
});

// global search
document.getElementById('masterSearchInput').addEventListener('keyup', function(e){
    if(e.key === 'Enter'){
        const keyword = this.value.trim();
        const url = new URL(window.location.href);
        if(keyword) url.searchParams.set('search', keyword);
        else url.searchParams.delete('search');
        window.location.href = url.href;
    }
});

// hide loader
window.addEventListener('load',()=> {
    document.getElementById('pageLoader').classList.remove('show');
});
</script>

@stack('scripts')
</body>
</html>
