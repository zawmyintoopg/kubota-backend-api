<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MaxPos | Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- MDB UI Kit -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
    background:linear-gradient(135deg,#4e73df,#224abe);
    min-height:100vh;
}

/* CARD */
.card{
    border-radius:1rem;
}

/* INVALID INPUT */
.is-invalid{
    border-color:#dc3545 !important;
    box-shadow:0 0 0 .15rem rgba(220,53,69,.25);
}

/* SHAKE ANIMATION */
@keyframes shake{
    0%{transform:translateX(0)}
    25%{transform:translateX(-4px)}
    50%{transform:translateX(4px)}
    75%{transform:translateX(-4px)}
    100%{transform:translateX(0)}
}
.shake{ animation:shake .35s }

/* PAGE LOADER */
.page-loader{
    position:fixed;
    inset:0;
    background:linear-gradient(135deg,#4e73df,#224abe);
    display:none;
    align-items:center;
    justify-content:center;
    z-index:3000;
    color:#fff;
}
.page-loader.show{ display:flex }

/* LOADER LOGO */
.loader-logo{
    width:80px;
    height:80px;
    background:#fff;
    color:#4e73df;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:34px;
    margin:0 auto 16px;
    animation:pulse 1.5s infinite, rotate 3s linear infinite;
}
@keyframes pulse{
    0%{transform:scale(1)}
    50%{transform:scale(1.1)}
    100%{transform:scale(1)}
}
@keyframes rotate{
    0%{transform:rotate(0deg)}
    100%{transform:rotate(360deg)}
}
</style>
</head>

<body class="d-flex justify-content-center align-items-center">

<!-- LOADER -->
<div id="pageLoader" class="page-loader">
    <div class="text-center">
        <div class="loader-logo">
            <i class="fas fa-store"></i>
        </div>
        <div>Signing in…</div>
    </div>
</div>

<!-- LOGIN CARD -->
<div class="col-md-4 col-sm-10">
    <div class="card shadow-5">
        <div class="card-body p-4">

            <h4 class="text-center mb-4 fw-bold">
                <i class="fas fa-cash-register me-1"></i> MaxPos Login
            </h4>

            <form method="POST"
                  action="{{ route('login_store') }}"
                  onsubmit="return startLogin()">
                @csrf

                <!-- EMAIL -->
                <div class="form-outline mb-3">
                    <input type="email"
                           id="inpemail"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid shake @enderror"
                           required>
                    <label class="form-label">Email</label>
                </div>

                <!-- PASSWORD -->
                <div class="form-outline mb-4">
                    <input type="password"
                           id="inppassword"
                           name="password"
                           class="form-control @error('password') is-invalid shake @enderror"
                           required>
                    <label class="form-label">Password</label>
                </div>

                <button type="submit"
                        id="loginBtn"
                        class="btn btn-primary btn-block">
                    <span id="btnText">
                        <i class="fas fa-sign-in-alt me-1"></i> Login
                    </span>
                </button>

            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded',()=>{
    document.getElementById('inpemail').focus();

    // hide loader if validation error
    @if($errors->any())
        document.getElementById('pageLoader').classList.remove('show');
    @endif
});

function startLogin(){
    document.getElementById('loginBtn').disabled=true;
    document.getElementById('btnText').innerHTML=
        '<i class="fas fa-spinner fa-spin me-1"></i> Signing in…';
    document.getElementById('pageLoader').classList.add('show');
    return true;
}
</script>

<!-- SWEETALERT VALIDATION -->
@if($errors->any())
<script>
Swal.fire({
    icon:'error',
    title:'Login Failed',
    html:`
        <div style="text-align:left">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    `,
    confirmButtonText:'Try Again',
    confirmButtonColor:'#dc3545'
});

// focus first invalid
setTimeout(()=>{
    let el=document.querySelector('.is-invalid');
    if(el) el.focus();
},300);
</script>
@endif

</body>
</html>
