@extends('guest.layouts.app')

@section('title', 'Tambah Buyer - PAS Market')

@section('mobile-topbar-inner')
<a href="{{ url('/profile/my-customers') }}" class="login-mob-back">
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
.form-section {
    margin-bottom: 1.75rem;
}
.form-section-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #1e293b;
    padding-bottom: 0.6rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid #e2e8f0;
}
.form-section-title .icon-wrap {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
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
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.my-customers.index') }}">Pelanggan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">Tambah Buyer</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-secondary mt-3 mb-0">Tambah Buyer Baru</h1>
    </div>
</section>

<!-- Register Buyer Section (Desktop) -->
<section class="py-5 login-page mobile-hide">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card border-0 shadow-sm auth-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <h3 class="fw-bold text-primary">
                                    <i class="bi bi-person-plus"></i> Tambah Buyer
                                </h3>
                            </a>
                            <p class="text-muted mt-2">Buatkan akun buyer baru dengan data lengkap</p>
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

                        <form method="POST" action="{{ route('guest.register-buyer.store') }}" novalidate data-ajax="false">
                            @csrf

                            {{-- Informasi Login --}}
                            <div class="form-section">
                                <div class="form-section-title">
                                    <div class="icon-wrap" style="background:#dbeafe;color:#2563eb;"><i class="bi bi-shield-lock" style="font-size:0.7rem;"></i></div>
                                    Informasi Login
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="nama@email.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" required placeholder="Nama lengkap buyer">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" required placeholder="Min 8 karakter">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">Minimal 8 karakter</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required placeholder="Ulangi password">
                                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Identitas --}}
                            <div class="form-section">
                                <div class="form-section-title">
                                    <div class="icon-wrap" style="background:#fef3c7;color:#d97706;"><i class="bi bi-person-badge" style="font-size:0.7rem;"></i></div>
                                    Identitas
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">No HP <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Perusahaan</label>
                                        <input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}" placeholder="Opsional">
                                    </div>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="form-section">
                                <div class="form-section-title">
                                    <div class="icon-wrap" style="background:#d1fae5;color:#059669;"><i class="bi bi-geo-alt" style="font-size:0.7rem;"></i></div>
                                    Alamat
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="address" rows="2" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Provinsi</label>
                                        <input type="text" class="form-control" name="province" value="{{ old('province') }}" placeholder="Nama provinsi">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kota</label>
                                        <input type="text" class="form-control" name="city" value="{{ old('city') }}" placeholder="Nama kota">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Kode POS</label>
                                        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code') }}" placeholder="Kode POS">
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>Setelah form ini dikirim, kode verifikasi akan dikirim ke email buyer untuk verifikasi.
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3 py-2">
                                <i class="bi bi-person-plus me-2"></i>Kirim & Verifikasi Email
                            </button>
                            
                            <div class="text-center">
                                <a href="{{ route('guest.profile.my-customers.index') }}" class="text-decoration-none text-muted">Kembali ke daftar pelanggan</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE REGISTER BUYER ====================== -->
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
            <h2 class="login-mob-title">Tambah Buyer Baru</h2>
            <p class="login-mob-desc">Buatkan akun untuk buyer</p>
        </div>

        <div class="login-mob-scroll">
        <form method="POST" action="{{ route('guest.register-buyer.store') }}" novalidate data-ajax="false">
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
                    <i class="bi bi-building"></i>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Nama perusahaan (opsional)">
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
                    <input type="password" name="password" required placeholder="Password" id="mobPassword">
                    <button type="button" class="login-mob-pwd-toggle" id="mobTogglePassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>
            <div class="login-mob-field">
                <div class="login-mob-input-wrap">
                    <i class="bi bi-shield-lock"></i>
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi password" id="mobConfirmPassword">
                    <button type="button" class="login-mob-pwd-toggle" id="mobToggleConfirmPassword" tabindex="-1">
                        <i class="bi bi-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="login-mob-info" style="padding:10px 16px;background:#f0f4ff;border-radius:8px;margin:12px 0;font-size:12px;color:#555;">
                <i class="bi bi-info-circle me-1"></i>Kode verifikasi akan dikirim ke email buyer.
            </div>

            <button type="submit" class="login-mob-btn">Buat Akun</button>
        </form>
        </div>
    </div>

    <div class="login-mob-register">
        <a href="{{ route('guest.profile.my-customers.index') }}">Kembali ke daftar pelanggan</a>
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
