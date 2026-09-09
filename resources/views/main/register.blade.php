<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- MDB UI Kit -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>

<style>
body{
    background: linear-gradient(135deg,#1cc88a,#17a673);
    min-height:100vh;
}
.card{
    border-radius:1rem;
}
</style>
</head>

<body class="d-flex justify-content-center align-items-center">

<div class="col-md-4">
    <div class="card shadow-5">
        <div class="card-body p-4">

            <h4 class="text-center mb-4">
                <i class="fas fa-user-plus me-1"></i> Create Account
            </h4>

            <form method="POST" action="{{ route('maxpos_store') }}">
                @csrf

                <!-- Name -->
                <div class="form-outline mb-3">
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           autofocus>
                    <label class="form-label" for="name">Full Name</label>

                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-outline mb-3">
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    <label class="form-label" for="email">Email</label>

                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role"
                            class="form-select @error('role') is-invalid @enderror">
                        <option value="">-- Select Role --</option>
                        <option value="admin" {{ old('role')=='admin'?'selected':'' }}>Admin</option>
                        <option value="staff" {{ old('role')=='staff'?'selected':'' }}>Staff</option>
                        <option value="cashier" {{ old('role')=='cashier'?'selected':'' }}>Cashier</option>
                    </select>

                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-outline mb-3">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    <label class="form-label" for="password">Password</label>

                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-outline mb-3">
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           class="form-control">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                </div>

                <button type="submit" class="btn btn-success btn-block">
                    <i class="fas fa-user-check me-1"></i> Register
                </button>
            </form>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<!-- JS: Focus on Full Name on page load -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('name').focus();
});
</script>

</body>
</html>
