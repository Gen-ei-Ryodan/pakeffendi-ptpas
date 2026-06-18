@extends('guest.layouts.app')

@section('title', 'Verifikasi Email - PAS Market')

@section('mobile-topbar-inner')
<a href="{{ url('/login') }}" class="login-mob-back">
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
                <li class="breadcrumb-item"><a href="{{ route('guest.login') }}">Login</a></li>
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
                        <form method="POST" action="{{ route('guest.verify-email.store') }}" novalidate>
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
                        
                        <div class="text-center">
                            <p class="text-muted mb-0">
                                Belum menerima kode? 
                                <a href="{{ route('guest.login') }}" class="text-decoration-none">Login ulang untuk kirim ulang</a>
                            </p>
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

        <form method="POST" action="{{ route('guest.verify-email.store') }}" novalidate>
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
    </div>

    <div class="login-mob-register">
        <a href="{{ route('guest.login') }}">Login</a>
    </div>
</section>
@endsection
