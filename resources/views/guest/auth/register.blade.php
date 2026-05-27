@extends('guest.layouts.app')

@section('title', 'Daftar - PAS Market')

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
                <li class="breadcrumb-item active" aria-current="page">Daftar</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Register Section (Desktop) -->
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
                            <p class="text-muted mt-2">Buat akun baru untuk memulai belanja</p>
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
                        <form method="POST" action="{{ route('guest.register.store') }}" data-ajax="false" novalidate id="registerForm">
                            @csrf
                            <div class="mb-3">
                                <label for="fullName" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="fullName" name="full_name" value="{{ old('full_name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Konfirmasi Kata Sandi</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" value="1" required>
                                    <label class="form-check-label" for="terms">
                                        Saya menyetujui syarat dan ketentuan
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                            </button>
                        </form>
                        
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Sudah punya akun? 
                                <a href="{{ url('/login') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}" class="text-decoration-none text-primary fw-semibold">
                                    Login disini
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4 security-notice">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        Data Anda aman dengan enkripsi SSL 256-bit
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE REGISTER ====================== -->
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
                <i class="bi bi-person-plus"></i>
                <span>PAS</span>
            </div>
        </div>
        <div class="login-mob-head">
            <h2 class="login-mob-title">Buat Akun Baru</h2>
            <p class="login-mob-desc">Daftar untuk mulai belanja</p>
        </div>

        <div class="login-mob-scroll">
        <form method="POST" action="{{ route('guest.register.store') }}" data-ajax="false" novalidate id="mobRegisterForm">
            @csrf
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-person"></i>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="Nama lengkap">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-phone"></i>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-geo-alt"></i>
                    <textarea name="address" rows="2" placeholder="Alamat (opsional)">{{ old('address') }}</textarea>
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" required placeholder="Kata Sandi" id="mobPassword" autocomplete="new-password">
                    <button type="button" class="login-mob-pwd-toggle" id="mobTogglePassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-shield-lock"></i>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi kata sandi" id="mobConfirmPassword" autocomplete="new-password">
                    <button type="button" class="login-mob-pwd-toggle" id="mobToggleConfirmPassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <label class="login-mob-terms-check">
                <input type="checkbox" name="terms" value="1" required>
                <span>Saya setuju <a href="#">Syarat &amp; Ketentuan</a></span>
            </label>

            <button type="submit" class="login-mob-btn" id="mobRegisterBtn">Daftar</button>
        </form>
        </div>
    </div>

    <div class="login-mob-register">
        Sudah punya akun? <a href="{{ url('/login') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}">Login</a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupPwdToggle(toggleId, inputId) {
        const btn = document.getElementById(toggleId);
        const inp = document.getElementById(inputId);
        if (btn && inp) {
            btn.addEventListener('click', function() {
                const type = inp.getAttribute('type') === 'password' ? 'text' : 'password';
                inp.setAttribute('type', type);
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye-slash');
                icon.classList.toggle('bi-eye');
            });
        }
    }

    setupPwdToggle('togglePassword', 'password');
    setupPwdToggle('toggleConfirmPassword', 'confirmPassword');
    setupPwdToggle('mobTogglePassword', 'mobPassword');
    setupPwdToggle('mobToggleConfirmPassword', 'mobConfirmPassword');

    function setupRegisterForm(formId, btnId) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function(e) {
            const name = form.querySelector('[name="full_name"]');
            const email = form.querySelector('[name="email"]');
            const phone = form.querySelector('[name="phone"]');
            const pwd = form.querySelector('[name="password"]');
            const pwdConf = form.querySelector('[name="password_confirmation"]');
            const terms = form.querySelector('[name="terms"]');
            const btn = document.getElementById(btnId) || form.querySelector('button[type="submit"]');

            if (!name.value.trim()) { e.preventDefault(); alert('Mohon isi nama lengkap'); name.focus(); return; }
            if (!email.value.trim()) { e.preventDefault(); alert('Mohon isi email'); email.focus(); return; }
            if (!phone.value.trim()) { e.preventDefault(); alert('Mohon isi nomor HP'); phone.focus(); return; }
            if (pwd.value.length < 8) { e.preventDefault(); alert('Kata sandi minimal 8 karakter'); pwd.focus(); return; }
            if (pwd.value !== pwdConf.value) { e.preventDefault(); alert('Konfirmasi kata sandi tidak cocok'); pwdConf.focus(); return; }
            if (!terms.checked) { e.preventDefault(); alert('Setujui syarat dan ketentuan'); terms.focus(); return; }
            if (btn) { btn.disabled = true; btn.innerHTML = 'Memproses...'; }
        });
    }

    setupRegisterForm('registerForm', null);
    setupRegisterForm('mobRegisterForm', 'mobRegisterBtn');
});
</script>
@endpush
