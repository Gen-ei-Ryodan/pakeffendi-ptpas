@extends('guest.layouts.app')

@section('title', 'Ubah Password - PAS Market')

@section('mobile-topbar-inner')
<a href="{{ url('/profile') }}" class="login-mob-back">
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
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ubah Password</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-secondary mt-3 mb-0">Ubah Password</h1>
    </div>
</section>

<!-- Change Password Section (Desktop) -->
<section class="py-5 login-page mobile-hide">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">
                                <i class="bi bi-key"></i> Ubah Password
                            </h3>
                            <p class="text-muted mt-2">Verifikasi email untuk mengubah password</p>
                        </div>
                        
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($step === 'send_code')
                        <form method="POST" action="{{ route('guest.change-password.send-code') }}" novalidate>
                            @csrf
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Klik tombol di bawah untuk mengirim kode verifikasi ke email <strong>{{ $customer->email }}</strong>.
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-send me-2"></i>Kirim Kode Verifikasi
                            </button>
                        </form>
                        @elseif($step === 'verify')
                        <form method="POST" action="{{ route('guest.change-password.store') }}" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Verifikasi</label>
                                <input type="text" class="form-control text-center" id="code" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required 
                                       style="font-size:1.5rem;letter-spacing:8px;font-weight:bold;">
                                <small class="text-muted">Masukkan kode 6 digit yang dikirim ke email Anda</small>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>Ubah Password
                            </button>
                        </form>
                        @endif
                        
                        <div class="text-center">
                            <a href="{{ route('guest.profile.index') }}" class="text-decoration-none">Kembali ke Profil</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE CHANGE PASSWORD ====================== -->
<section class="d-lg-none" id="mobLoginPage">
    @if(session('success'))
    <div class="login-mob-alert">
        <div class="login-mob-alert-item" style="background:#d1e7dd;color:#0a3622;">{{ session('success') }}</div>
    </div>
    @endif
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
                <i class="bi bi-key"></i>
                <span>PAS</span>
            </div>
        </div>
        <div class="login-mob-head">
            <h2 class="login-mob-title">Ubah Password</h2>
            <p class="login-mob-desc">Verifikasi email untuk melanjutkan</p>
        </div>

        @if($step === 'send_code')
        <form method="POST" action="{{ route('guest.change-password.send-code') }}" novalidate>
            @csrf
            <div class="login-mob-info" style="padding:10px 16px;background:#cff4fc;border-radius:8px;margin:12px 0;font-size:13px;color:#055160;">
                <i class="bi bi-info-circle me-1"></i>Kode verifikasi akan dikirim ke <strong>{{ $customer->email }}</strong>
            </div>
            <button type="submit" class="login-mob-btn">Kirim Kode Verifikasi</button>
        </form>
        @elseif($step === 'verify')
        <form method="POST" action="{{ route('guest.change-password.store') }}" novalidate>
            @csrf
            <div class="login-mob-field">
                <div class="login-mob-input-wrap" style="text-align:center;">
                    <input type="text" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required
                           style="text-align:center;font-size:1.5rem;letter-spacing:8px;font-weight:bold;padding:12px;">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" required placeholder="Password baru" id="mobPassword" minlength="8">
                    <button type="button" class="login-mob-pwd-toggle" id="mobTogglePassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-shield-lock"></i>
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi password baru" id="mobConfirmPassword" minlength="8">
                    <button type="button" class="login-mob-pwd-toggle" id="mobToggleConfirmPassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="login-mob-btn">Ubah Password</button>
        </form>
        @endif
    </div>

    <div class="login-mob-register">
        <a href="{{ route('guest.profile.index') }}">Kembali ke Profil</a>
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
});
</script>
@endpush
