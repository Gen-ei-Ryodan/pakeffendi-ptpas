@extends('guest.layouts.app')

@section('title', 'Login - PAS Market')

@push('styles')
    <!-- Custom CSS -->
    <link href="{{ asset('guest/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('guest/css/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Login</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Login Section -->
<section class="py-5 login-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm auth-card">
                    <div class="card-body p-5">
                        <!-- Logo/Brand -->
                        <div class="text-center mb-4">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <h3 class="fw-bold text-primary">
                                    <i class="bi bi-shop"></i> PAS Market
                                </h3>
                            </a>
                            <p class="text-muted mt-2">Silakan login untuk melanjutkan</p>
                        </div>
                        
                        <!-- Login Form -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('guest.login.store') }}" data-ajax="false" novalidate id="loginForm">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email atau Nomor HP</label>
                                <input type="text" class="form-control" id="email" name="login" value="{{ old('login') }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" value="1" @checked(old('remember'))>
                                    <label class="form-check-label" for="rememberMe">
                                        Ingat saya
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>
                        
                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Belum punya akun? 
                                <a href="{{ url('/register') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}" class="text-decoration-none text-primary fw-semibold">
                                    Daftar sekarang
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Security Notice -->
                <div class="text-center mt-4 security-notice">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Transaksi Anda aman dengan enkripsi SSL 256-bit
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const submitBtn = loginForm?.querySelector('button[type="submit"]');
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash');
        });
    }
    
    // Form validation and submission
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const loginInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Basic validation
            if (!loginInput.value.trim()) {
                e.preventDefault();
                alert('Mohon isi email atau nomor HP');
                loginInput.focus();
                return;
            }
            
            if (!passwordInput.value.trim()) {
                e.preventDefault();
                alert('Mohon isi kata sandi');
                passwordInput.focus();
                return;
            }
            
            // Disable submit button to prevent double submission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Memproses...';
            }
        });
    }
});
</script>
@endpush
