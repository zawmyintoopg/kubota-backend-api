<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>MaxPos | Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- FontAwesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Body */
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg,#4e73df,#224abe);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Card */
.login-card {
    background: #fff;
    padding: 30px 25px;
    border-radius: 15px;
    width: 350px;
    box-shadow: 0 15px 25px rgba(0,0,0,0.3);
}

/* Card Header */
.login-card h2 {
    text-align: center;
    margin-bottom: 25px;
    font-size: 1.5rem;
    color: #224abe;
}

/* Inputs */
.login-card input[type="email"],
.login-card input[type="password"] {
    width: 100%;
    padding: 6px;
    margin: 10px 0 20px;
    border: 1.5px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    transition: border 0.3s, box-shadow 0.3s;
}

.login-card input:focus {
    border-color: #224abe;
    box-shadow: 0 0 5px rgba(34,74,190,0.5);
    outline: none;
}

/* Invalid input shake */
.is-invalid {
    border-color: #dc3545 !important;
    animation: shake .35s;
}

@keyframes shake {
    0% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    50% { transform: translateX(5px); }
    75% { transform: translateX(-5px); }
    100% { transform: translateX(0); }
}

/* Button */
.login-card button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background-color: #224abe;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-card button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.login-card button .spinner {
    display: none;
    border: 3px solid #fff;
    border-top: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    width: 18px;
    height: 18px;
    animation: spin 1s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Page Loader */
.page-loader {
    position: fixed;
    inset: 0;
    background: linear-gradient(135deg,#4e73df,#224abe);
    display: none;
    align-items: center;
    justify-content: center;
    color: #fff;
    z-index: 2000;
}

.page-loader.show {
    display: flex;
}

.loader-logo {
    width: 80px;
    height: 80px;
    background: #fff;
    color: #224abe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    margin-bottom: 15px;
    animation: pulse 1.5s infinite, rotate 3s linear infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</head>
<body>

<!-- Loader -->
<div id="pageLoader" class="page-loader">
    <div class="text-center">
        <div class="loader-logo"><i class="fas fa-store"></i></div>
        <div>Signing in…</div>
    </div>
</div>

<!-- Login Card -->
<div class="login-card">
    <h2><i class="fas fa-cash-register"></i> MaxPos Login</h2>
    <form method="POST" action="{{ route('login_store') }}" onsubmit="return startLogin()">
        @csrf

        <input type="email" name="email" id="emailInput" placeholder="Email" value="{{ old('email') }}" required>
        <input type="password" name="password" id="passwordInput" placeholder="Password" required>

        <button type="submit" id="loginBtn">
            <div class="spinner" id="btnSpinner"></div>
            <span id="btnText">Login</span>
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('emailInput').focus();

    @if($errors->any())
        document.getElementById('pageLoader').classList.remove('show');
    @endif
});

function startLogin() {
    const btn = document.getElementById('loginBtn');
    const spinner = document.getElementById('btnSpinner');
    const text = document.getElementById('btnText');

    btn.disabled = true;
    spinner.style.display = 'inline-block';
    text.textContent = 'Signing in…';

    document.getElementById('pageLoader').classList.add('show');

    return true;
}
</script>

@if($errors->any())
<script>
Swal.fire({
    icon: 'error',
    title: 'Login Failed',
    html: `
        <div style="text-align:left">
            @foreach($errors->all() as $error)
                <div>• {{ $error }}</div>
            @endforeach
        </div>
    `,
    confirmButtonText: 'Try Again',
    confirmButtonColor: '#dc3545'
});
setTimeout(()=>{
    let el=document.querySelector('.is-invalid');
    if(el) el.focus();
},300);
</script>
@endif

</body>
</html>
