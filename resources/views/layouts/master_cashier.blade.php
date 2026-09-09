<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- ================= LOCAL CSS ================= -->
<!-- MDB UI KIT -->
<link href="{{ asset('assets/vendor/mdb/mdb.min.css') }}" rel="stylesheet"/>

<!-- Font Awesome 7 Local -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
/* HEADER */
.app-header{
    height:56px;
    position:sticky;
    top:0;
    z-index:1045;
    background:#fff;
    padding:0 15px;
    display:flex;
    align-items:center;
}

/* SEARCH */
.search-wrapper{ flex:1; max-width:70%; }
.search-input{ border-radius:30px; }

/* APP MENU */
#appMenu{
    position:absolute;
    top:52px;
    left:10px;
    width:720px;
    background:#fff;
    border-radius:16px;
    box-shadow:0 15px 40px rgba(0,0,0,.18);
    padding:16px;
    opacity:0;
    transform:scale(.95);
    pointer-events:none;
    transition:.25s ease;
    z-index:1050;
}
#appMenu.show{
    opacity:1;
    transform:scale(1);
    pointer-events:auto;
}

.app-search{ position:relative; margin-bottom:14px; }
.app-search input{ border-radius:12px; padding-left:38px; }
.app-search i{
    position:absolute;
    top:50%;
    left:14px;
    transform:translateY(-50%);
    color:#6c757d;
}

.app-grid{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:14px;
}

.app-item{
    text-align:center;
    text-decoration:none;
    color:#212529;
    padding:12px 6px;
    border-radius:12px;
    transition:.2s;
}
.app-item i{ font-size:26px; margin-bottom:6px; }
.app-item span{ font-size:12px; display:block; }

.app-item:hover,
.app-item.active{
    background:#f3f6ff;
    transform:translateY(-2px);
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

/* MOBILE */
@media(max-width:768px){
    #appMenu{ width:92vw; left:4vw; }
    .app-grid{ grid-template-columns:repeat(2,1fr); }
    .search-wrapper{ max-width:60%; }
}
</style>
</head>

<body class="bg-light">

<!-- PAGE LOADING SPINNER -->
<div id="pageLoader" class="page-loader">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<div class="container-fluid">

<nav class="navbar bg-primary shadow-1 app-header">

    <div class="position-relative">
        <button class="btn text-white" ata-bs-toggle="tooltip" data-bs-placement="top" title="Menu" onclick="toggleMenu()">
            <i class="fas fa-grip fs-5"></i>
        </button>

        <div id="appMenu">
            <div class="app-search">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control " placeholder="Find apps..." onkeyup="filterApps(this.value)">
            </div>

            <div class="app-grid" id="appGrid">
                <a href="" class="app-item">
                    <i class="fas fa-home text-primary"></i>
                    <span>Dashboard</span>
                </a>

                @auth
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('product_master.category') }}" class="app-item">
                    <i class="fas fa-tags text-success"></i>
                    <span>Category</span>
                </a>
                
                <a href="{{ route('product_master.unit') }}" class="app-item">
                    <i class="fas fa-balance-scale text-info"></i>
                    <span>Unit</span>
                </a>
                
                <a href="{{ route('product_master.brand') }}" class="app-item">
                    <i class="fas fa-copyright text-dark"></i>
                    <span>Brand</span>
                </a>
                <a href="{{ route('product_master.product') }}" class="app-item">
                    <i class="fas fa-box-open text-warning"></i>
                    <span>Product</span>
                </a>
                <a href="{{ route('common.payment_method') }}" class="app-item">
                    <i class="fas fa-credit-card text-success"></i>
                    <span>Payment Method</span>
                </a>

                <a href="{{ route('common.transaction_type') }}" class="app-item">
                    <i class="fas fa-exchange-alt text-info"></i>
                    <span>Transaction Type</span>
                </a>
                <a href="{{ route('purchase_master.supplier') }}" class="app-item">
                    <i class="fas fa-truck text-secondary"></i>
                    <span>Supplier</span>
                </a>
                <a href="{{ route('purchase_master.purchase') }}" class="app-item">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    <span>Purchase</span>
                </a>
                
                            <!-- Stock Movement -->
                <a href="{{ route('stock-movements.index') }}" class="app-item">
                    <i class="fas fa-boxes text-warning"></i>
                    <span>Stock Movement</span>
                </a>
                @endif
                @if(auth()->user()->role === 'cashier')
                <a href="{{ route('sale_master.sales') }}" class="app-item">
                    <i class="fas fa-cash-register text-primary"></i>
                    <span>Sale</span>
                </a>
                <a href="#" class="app-item">
                    <i class="fas fa-chart-line text-danger"></i>
                    <span>Reports</span>
                </a>
                @endif
                @endauth
            </div>
        </div>
    </div>

    <div class="search-wrapper ms-3">
        <input type="text" id="masterSearchInput" class="form-control search-input form-control-md" placeholder="Search..." value="{{ request('search') }}">
    </div>

    <!-- USER PROFILE DROPDOWN -->
    <div class="ms-auto dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="userDropdown" data-mdb-toggle="dropdown" aria-expanded="false">
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

<div class="p-3">
    @yield('content')
</div>

</div>

<!-- ================= LOCAL JS ================= -->
<!-- MDB JS -->
<!-- <script src="{{ asset('assets/vendor/mdb/mdb.es.min.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
/* APP MENU */
function toggleMenu(){
    document.getElementById('appMenu').classList.toggle('show');
}
document.addEventListener('click', e => {
    const menu=document.getElementById('appMenu');
    if(!menu.contains(e.target) && !e.target.closest('.fa-grip')){
        menu.classList.remove('show');
    }
});
function filterApps(v){
    v=v.toLowerCase();
    document.querySelectorAll('#appGrid .app-item').forEach(a=>{
        a.style.display=a.innerText.toLowerCase().includes(v)?'':'none';
    });
}

/* MASTER SEARCH FILTER FOR TABLE */
document.getElementById('masterSearchInput').addEventListener('keyup', function(e){
    if(e.key === 'Enter'){
        const keyword = this.value.trim();
        const url = new URL(window.location.href);
        if(keyword) url.searchParams.set('search', keyword);
        else url.searchParams.delete('search');
        window.location.href = url.href;
    }
});

/* PAGE LOADER AUTO HIDE */
window.addEventListener('load',()=> {
    document.getElementById('pageLoader').classList.remove('show');
});
</script>

@stack('scripts')
</body>
</html>
