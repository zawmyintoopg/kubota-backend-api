<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- MDB UI KIT -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
.app-header{
    height:56px;
    position:sticky;
    top:0;
    z-index:1045;
    background:#fff;
}

.search-wrapper{ flex:1; max-width:70%; }
.search-input{ border-radius:30px; }

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

@media(max-width:768px){
    #appMenu{ width:92vw; left:4vw; }
    .app-grid{ grid-template-columns:repeat(2,1fr); }
}
</style>
</head>

<body class="bg-light">
<div class="container-fluid">

<nav class="navbar bg-primary shadow-1 px-3 app-header d-flex align-items-center">

    <div class="position-relative">
        <button class="btn text-white" onclick="toggleMenu()">
            <i class="fas fa-grip fs-5"></i>
        </button>

        <div id="appMenu">
            <div class="app-search">
                <i class="fas fa-search"></i>
                <input type="text" class="form-control" placeholder="Find apps..."
                       onkeyup="filterApps(this.value)">
            </div>

            <div class="app-grid" id="appGrid">

                <a href="{{ route('dashboard') }}" class="app-item">
                    <i class="fas fa-home text-primary"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('product_master.category') }}" class="app-item">
                    <i class="fas fa-tags text-success"></i>
                    <span>Category</span>
                </a>

                <a href="{{ route('product_master.unit') }}" class="app-item">
                    <i class="fas fa-balance-scale text-info"></i>
                    <span>Unit</span>
                </a>

                <a href="{{ route('product_master.product') }}" class="app-item">
                    <i class="fas fa-box-open text-warning"></i>
                    <span>Product</span>
                </a>

                <a href="{{ route('product_master.brand') }}" class="app-item">
                    <i class="fas fa-copyright text-dark"></i>
                    <span>Brand</span>
                </a>

                <a href="{{ route('purchase_master.purchase') }}" class="app-item">
                    <i class="fas fa-shopping-cart text-primary"></i>
                    <span>Purchase</span>
                </a>

                <a href="{{ route('purchase_master.supplier') }}" class="app-item">
                    <i class="fas fa-truck text-secondary"></i>
                    <span>Supplier</span>
                </a>

                <!-- ✅ FIXED PRICE HISTORY -->
                <a href="{{ route('price.history.index') }}"
                   class="app-item {{ request()->routeIs('price.history.*') ? 'active' : '' }}">
                    <i class="fas fa-clock text-warning"></i>
                    <span>Price History</span>
                </a>

                <a href="#" class="app-item">
                    <i class="fas fa-chart-line text-danger"></i>
                    <span>Reports</span>
                </a>

            </div>
        </div>
    </div>

    <div class="search-wrapper ms-3">
        <input type="text" class="form-control search-input" placeholder="Search...">
    </div>

    <div class="ms-auto d-flex gap-2">
        <button class="btn btn-light"><i class="fas fa-bell"></i></button>
        <button class="btn btn-light"><i class="fas fa-user"></i></button>
    </div>
</nav>

<div class="p-3">
    @yield('content')
</div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
function toggleMenu(){
    document.getElementById('appMenu').classList.toggle('show');
}
document.addEventListener('click',e=>{
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
</script>

@stack('scripts')
</body>
</html>
