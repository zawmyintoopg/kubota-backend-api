<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title','Master Layout')</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Materialize CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet"/>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>

<style>
/* MASTER WRAPPER */
#masterWrapper {
    width: 100%;
    min-height: 100vh;
    margin: 0 auto;
    transition: all 0.3s ease;
}

/* Tablet mode box */
.tablet-container {
    width: 1024px;
    min-height: 768px;
    margin: 20px auto;
    border: 2px solid #1976d2;
    border-radius: 10px;
    background: #fff;
    padding: 15px;
}

/* HEADER */
.app-header {
    height: 56px;
    position: sticky;
    top: 0;
    z-index: 1045;
    background: #1976d2;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 15px;
    border-radius: 6px;
}

/* BUTTONS */
.btn-tablet {
    margin-left: 10px;
}

/* MENU GRID */
#appMenu {
    display: none;
    padding: 10px;
    background: #f5f5f5;
    margin-top: 5px;
    border-radius: 8px;
}

/* PAGE CONTENT */
.page-content {
    margin-top: 15px;
}

/* TABLET MODE RESPONSIVE */
@media(max-width:1024px){
    .tablet-container {
        width: 100%;
        margin: 5px;
        min-height: auto;
    }
}
</style>
</head>
<body class="grey lighten-4">

<div id="masterWrapper">

    <!-- HEADER -->
    <nav class="app-header">
        <div class="valign-wrapper">
            <i class="fas fa-grip fs-5 me-2" style="cursor:pointer" onclick="toggleMenu()"></i>
            <span>@yield('title','Master Layout')</span>
        </div>

        <div class="valign-wrapper">
            <a id="tabletModeBtn" class="btn blue btn-small btn-tablet">
                <i class="fas fa-tablet-alt"></i> Exit Tablet Mode
            </a>

            <a class="btn dropdown-trigger btn-small grey lighten-3 black-text ms-2" href="#!" data-target="userDropdown">
                <i class="fas fa-user"></i> Admin <i class="fas fa-caret-down right"></i>
            </a>
            <!-- Dropdown Structure -->
            <ul id="userDropdown" class="dropdown-content">
                <li><a href="#">Profile</a></li>
                <li><a href="#">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- MENU GRID -->
    <div id="appMenu" class="row">
        <div class="col s3 center-align">
            <a href="#" class="black-text">
                <i class="fas fa-home fa-2x"></i><br>Dashboard
            </a>
        </div>
        <div class="col s3 center-align">
            <a href="#" class="black-text">
                <i class="fas fa-cart-plus fa-2x"></i><br>Purchase
            </a>
        </div>
        <div class="col s3 center-align">
            <a href="#" class="black-text">
                <i class="fas fa-box-open fa-2x"></i><br>Product
            </a>
        </div>
        <div class="col s3 center-align">
            <a href="#" class="black-text">
                <i class="fas fa-chart-line fa-2x"></i><br>Reports
            </a>
        </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">
        @yield('content')
        @if(!trim($__env->yieldContent('content')))
            <h3 class="center-align">This is Master Layout Content</h3>
            <p class="center-align">Use a child page to display your real content here.</p>
        @endif
    </div>
</div>

<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dropdown
    var elems = document.querySelectorAll('.dropdown-trigger');
    M.Dropdown.init(elems, {coverTrigger: false});

    // Toggle menu grid
    window.toggleMenu = function() {
        const menu = document.getElementById('appMenu');
        menu.style.display = menu.style.display === 'none' ? 'flex' : 'none';
    }

    // Tablet mode toggle
    const tabletBtn = document.getElementById('tabletModeBtn');
    const wrapper = document.getElementById('masterWrapper');

    if(localStorage.getItem('tabletMode') === 'disabled'){
        wrapper.classList.remove('tablet-container');
        tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Enable Tablet Mode';
        tabletBtn.classList.remove('blue');
        tabletBtn.classList.add('grey');
    } else {
        wrapper.classList.add('tablet-container');
        tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Exit Tablet Mode';
    }

    tabletBtn.addEventListener('click', function(){
        if(wrapper.classList.contains('tablet-container')){
            wrapper.classList.remove('tablet-container');
            tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Enable Tablet Mode';
            tabletBtn.classList.remove('blue');
            tabletBtn.classList.add('grey');
            localStorage.setItem('tabletMode','disabled');
        } else {
            wrapper.classList.add('tablet-container');
            tabletBtn.innerHTML = '<i class="fas fa-tablet-alt"></i> Exit Tablet Mode';
            tabletBtn.classList.remove('grey');
            tabletBtn.classList.add('blue');
            localStorage.setItem('tabletMode','enabled');
        }
    });
});
</script>

</body>
</html>
