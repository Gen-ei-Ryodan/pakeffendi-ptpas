@php
    $backUrl = $mode === 'register' ? url('/register-buyer') : url('/login');
    $backText = $mode === 'register' ? 'Tambah Buyer' : 'Login';
@endphp
@extends('guest.layouts.app')

@section('title', 'Verifikasi Email - PAS Market')

@section('mobile-topbar-inner')
<a href="{{ $backUrl }}" class="login-mob-back">
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
<style>
.otp-countdown {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #f0f4ff;
    border-radius: 20px;
    font-size: 13px;
    color: #003366;
    font-weight: 500;
}
.otp-countdown .countdown-num {
    font-weight: 700;
    font-size: 15px;
    min-width: 28px;
    text-align: center;
}
.otp-countdown.expired {
    background: #fff0f0;
    color: #cc0000;
}
.resend-btn {
    background: none;
    border: none;
    color: #003366;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
}
.resend-btn:disabled {
    color: #999;
    cursor: not-allowed;
    text-decoration: none;
}
.resend-btn .countdown-text {
    color: #666;
    text-decoration: none;
}
</style>
@endpush

@section('content')
<!-- Page Header (Desktop) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ $backUrl }}">{{ $backText }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Verifikasi Email</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Verify Email Section (Desktop) -->
<section class="py-5 login-page mobile-hide">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <h3 class="fw-bold text-primary">
                                    <i class="bi bi-envelope-check"></i> Verifikasi Email
                                </h3>
                            </a>
                            <p class="text-muted mt-2">Masukkan kode verifikasi yang telah dikirim ke email Anda</p>
                        </div>

                        <div class="text-center mb-4">
                            <div class="otp-countdown" id="otpCountdown" {{ $otpExpiresAt ? '' : 'style=display:none' }}>
                                <i class="bi bi-clock"></i>
                                <span>Kode berlaku </span>
                                <span class="countdown-num" id="countdownDisplay">1:00</span>
                            </div>
                        </div>
                        
                        @if(session('info'))
                            <div class="alert alert-info">{{ session('info') }}</div>
                        @endif
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
                        <form method="POST" action="{{ route('guest.verify-email.store') }}" novalidate data-ajax="false">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $email) }}" required readonly>
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Verifikasi</label>
                                <input type="text" class="form-control text-center" id="code" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required 
                                       style="font-size:1.5rem;letter-spacing:8px;font-weight:bold;">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-check-circle me-2"></i>Verifikasi Email
                            </button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <form method="POST" action="{{ route('guest.verify-email.resend') }}" data-ajax="false" id="resendForm" class="d-inline">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <button type="submit" id="resendBtn" class="resend-btn" disabled>
                                    <span id="resendText">Kirim ulang kode</span>
                                    <span id="resendCountdown" class="countdown-text"> (<span id="resendTimer">60</span>s)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE VERIFY EMAIL ====================== -->
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
                <i class="bi bi-envelope-check"></i>
                <span>PAS</span>
            </div>
        </div>
        <div class="login-mob-head">
            <h2 class="login-mob-title">Verifikasi Email</h2>
            <p class="login-mob-desc">Masukkan kode dari email Anda</p>
        </div>

        <div class="text-center mb-3">
            <div class="otp-countdown" id="mobOtpCountdown" {{ $otpExpiresAt ? '' : 'style=display:none' }}>
                <i class="bi bi-clock"></i>
                <span>Kode berlaku </span>
                <span class="countdown-num" id="mobCountdownDisplay">1:00</span>
            </div>
        </div>

        <form method="POST" action="{{ route('guest.verify-email.store') }}" novalidate data-ajax="false">
            @csrf
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" value="{{ old('email', $email) }}" readonly required placeholder="Email">
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap" style="text-align:center;">
                    <input type="text" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required
                           style="text-align:center;font-size:1.5rem;letter-spacing:8px;font-weight:bold;padding:12px;">
                </div>
            </div>
            <button type="submit" class="login-mob-btn">Verifikasi</button>
        </form>

        <div class="text-center mt-3">
            <form method="POST" action="{{ route('guest.verify-email.resend') }}" data-ajax="false" class="d-inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" id="mobResendBtn" class="resend-btn" style="font-size:13px;color:#999!important;" disabled>
                    <span id="mobResendText">Kirim ulang kode</span>
                    <span id="mobResendCountdown" class="countdown-text"> (<span id="mobResendTimer">60</span>s)</span>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var otpExpiresAt = {{ $otpExpiresAt ? $otpExpiresAt : 'null' }};
    var resendCooldown = 60;
    var resendTimer = resendCooldown;

    function updateCountdown() {
        if (!otpExpiresAt) return;

        var now = Math.floor(Date.now() / 1000);
        var diff = otpExpiresAt - now;
        
        var display = document.getElementById('countdownDisplay');
        var mobDisplay = document.getElementById('mobCountdownDisplay');
        var container = document.getElementById('otpCountdown');
        var mobContainer = document.getElementById('mobOtpCountdown');

        if (diff <= 0) {
            var expiredText = 'Kedaluwarsa';
            if (display) { display.textContent = expiredText; container.className = 'otp-countdown expired'; }
            if (mobDisplay) { mobDisplay.textContent = expiredText; mobContainer.className = 'otp-countdown expired'; }
            return;
        }

        var minutes = Math.floor(diff / 60);
        var seconds = diff % 60;
        var timeStr = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

        if (display) display.textContent = timeStr;
        if (mobDisplay) mobDisplay.textContent = timeStr;
    }

    function updateResendTimer() {
        var btn = document.getElementById('resendBtn');
        var mobBtn = document.getElementById('mobResendBtn');
        var timer = document.getElementById('resendTimer');
        var mobTimer = document.getElementById('mobResendTimer');
        var text = document.getElementById('resendText');
        var mobText = document.getElementById('mobResendText');
        var countdown = document.getElementById('resendCountdown');
        var mobCountdown = document.getElementById('mobResendCountdown');

        if (resendTimer > 0) {
            if (timer) timer.textContent = resendTimer;
            if (mobTimer) mobTimer.textContent = resendTimer;
            if (btn) btn.disabled = true;
            if (mobBtn) mobBtn.disabled = true;
            if (countdown) countdown.style.display = 'inline';
            if (mobCountdown) mobCountdown.style.display = 'inline';
            resendTimer--;
        } else {
            if (btn) { btn.disabled = false; if (countdown) countdown.style.display = 'none'; }
            if (mobBtn) { mobBtn.disabled = false; if (mobCountdown) mobCountdown.style.display = 'none'; }
            if (text) text.textContent = 'Kirim ulang kode';
            if (mobText) mobText.textContent = 'Kirim ulang kode';
        }
    }

    // Run countdown every second
    if (otpExpiresAt) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    // Run resend timer every second
    updateResendTimer();
    setInterval(updateResendTimer, 1000);

    // Reset resend timer on form submit
    document.getElementById('resendForm')?.addEventListener('submit', function() {
        resendTimer = resendCooldown;
        updateResendTimer();
    });
});
</script>
@endpush
