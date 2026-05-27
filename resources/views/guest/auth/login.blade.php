@extends('guest.layouts.app')

@section('title', 'Login - PAS Market')

@section('mobile-topbar-inner')
<a href="{{ url('/') }}" class="login-mob-back">
    <i class="bi bi-chevron-left"></i>
</a>
<div class="login-mob-brand">
    <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
</div>
<a href="#" class="login-mob-help">
    <small>Bantuan</small>
</a>
@endsection

@push('styles')
<link href="{{ asset('guest/css/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header (Desktop) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Login</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Login Section (Desktop) -->
<section class="py-5 login-page mobile-hide">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <h3 class="fw-bold text-primary">
                                    <i class="bi bi-shop"></i> PAS Market
                                </h3>
                            </a>
                            <p class="text-muted mt-2">Silakan login untuk melanjutkan</p>
                        </div>

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
                                    <label class="form-check-label" for="rememberMe">Ingat saya</label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Belum punya akun?
                                <a href="{{ url('/register') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}" class="text-decoration-none text-primary fw-semibold">Daftar sekarang</a>
                            </p>
                        </div>
                    </div>
                </div>

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

<!-- ====================== MOBILE LOGIN ====================== -->
<section class="d-lg-none" id="mobLoginPage">
    @if($errors->any())
    <div class="login-mob-alert">
        @foreach($errors->all() as $error)
        <div class="login-mob-alert-item">{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <div class="login-mob-inner">
        <div class="login-mob-logo-wrap">
            <div class="login-mob-logo-box">
                <i class="bi bi-shop"></i>
                <span>PAS</span>
            </div>
        </div>
        <div class="login-mob-head">
            <h2 class="login-mob-title">Selamat Datang</h2>
            <p class="login-mob-desc">Masuk untuk melanjutkan belanja</p>
        </div>

        <form method="POST" action="{{ route('guest.login.store') }}" data-ajax="false" novalidate id="mobLoginForm">
            @csrf
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="text" name="login" value="{{ old('login') }}" required placeholder="Email atau nomor HP" autocomplete="username">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" required placeholder="Kata Sandi" id="mobPassword" autocomplete="current-password">
                    <button type="button" class="login-mob-pwd-toggle" id="mobTogglePassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="login-mob-forgot">
                <a href="#">Lupa password?</a>
            </div>

            <button type="submit" class="login-mob-btn" id="mobLoginBtn">Masuk</button>
        </form>

        <p class="login-mob-terms">Dengan masuk, Anda menyetujui <a href="#">Syarat &amp; Ketentuan</a></p>
    </div>

    <div class="login-mob-register">
        Belum punya akun? <a href="{{ url('/register') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}">Daftar</a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('mobTogglePassword');
    const pwd = document.getElementById('mobPassword');
    if (toggle && pwd) {
        toggle.addEventListener('click', function() {
            const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
            pwd.setAttribute('type', type);
            const icon = this.querySelector('i');
            icon.classList.toggle('bi-eye-slash');
            icon.classList.toggle('bi-eye');
        });
    }

    const form = document.getElementById('mobLoginForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const login = form.querySelector('[name="login"]');
            const password = form.querySelector('[name="password"]');
            const btn = document.getElementById('mobLoginBtn');
            if (!login.value.trim()) { e.preventDefault(); alert('Mohon isi email atau nomor HP'); login.focus(); return; }
            if (!password.value.trim()) { e.preventDefault(); alert('Mohon isi kata sandi'); password.focus(); return; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Memproses...'; }
        });
    }

    const formDesktop = document.getElementById('loginForm');
    if (formDesktop) {
        formDesktop.addEventListener('submit', function(e) {
            const login = formDesktop.querySelector('[name="login"]');
            const password = formDesktop.querySelector('[name="password"]');
            const btn = formDesktop.querySelector('button[type="submit"]');
            if (!login.value.trim()) { e.preventDefault(); alert('Mohon isi email atau nomor HP'); login.focus(); return; }
            if (!password.value.trim()) { e.preventDefault(); alert('Mohon isi kata sandi'); password.focus(); return; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Memproses...'; }
        });
    }
});
</script>
@endpush
