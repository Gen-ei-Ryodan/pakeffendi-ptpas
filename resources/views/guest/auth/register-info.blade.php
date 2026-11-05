@extends('guest.layouts.app')

@section('title', 'Registrasi Customer Baru - PAS Market')

@php
    $loginUrl = url('/login') . (request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '');
@endphp

@section('mobile-topbar-inner')
<a href="{{ url('/') }}" class="login-mob-back">
    <i class="bi bi-chevron-left"></i>
</a>
<div class="login-mob-brand">
    <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
</div>
<a href="{{ url('/contact') }}" class="login-mob-help">
    <small>Bantuan</small>
</a>
@endsection

@push('styles')
<link href="{{ asset('guest/css/auth.css') }}?v={{ filemtime(public_path('guest/css/auth.css')) }}" rel="stylesheet">
@endpush

@section('content')
<!-- ====================== DESKTOP REGISTER INFO (split-screen) ====================== -->
<section class="auth-desktop mobile-hide" id="desktopRegisterInfo">
    <div class="auth-shell">
        <aside class="auth-aside">
            <a href="{{ url('/') }}" class="auth-aside-brand">
                <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
            </a>
            <div class="auth-aside-body">
                <span class="auth-aside-chip">Trading Floor B2B</span>
                <h2 class="auth-aside-title">Registrasi customer baru <em>bersama tim kami.</em></h2>
                <p class="auth-aside-desc">Akun buyer dibuat dan diverifikasi langsung oleh Sales/Admin PAS Market, sehingga Anda mendapat harga dan layanan yang tepat sejak awal.</p>
                <ul class="auth-aside-points">
                    <li><i class="bi bi-check-circle-fill"></i> Proses cepat, tanpa biaya pendaftaran</li>
                    <li><i class="bi bi-check-circle-fill"></i> Harga grosir khusus buyer terdaftar</li>
                    <li><i class="bi bi-check-circle-fill"></i> Didampingi sales selama proses order</li>
                </ul>
                <div class="auth-aside-stats">
                    <div class="stat"><strong>12K+</strong><span>Produk</span></div>
                    <div class="stat"><strong>5K+</strong><span>Buyer</span></div>
                    <div class="stat"><strong>34</strong><span>Provinsi</span></div>
                </div>
            </div>
            <div class="auth-aside-glow auth-aside-glow-1" aria-hidden="true"></div>
            <div class="auth-aside-glow auth-aside-glow-2" aria-hidden="true"></div>
        </aside>

        <main class="auth-main">
            <div class="auth-card-wrap">
                <div class="auth-head">
                    <div class="auth-brand-mark"><i class="bi bi-person-check"></i></div>
                    <h1 class="auth-title">Registrasi Customer Baru</h1>
                    <p class="auth-sub">Silakan hubungi Sales / Admin kami</p>
                </div>

                <div class="auth-notice">
                    <i class="bi bi-info-circle"></i>
                    <div>
                        Pendaftaran akun buyer <strong>tidak dapat dilakukan secara mandiri</strong>.
                        Tim Sales/Admin kami yang akan membuatkan akun Anda — cukup hubungi kami,
                        prosesnya hanya butuh beberapa menit.
                    </div>
                </div>

                <div class="auth-section-label">Hubungi Kami</div>
                <div class="auth-contacts">
                    <a href="https://wa.me/6285101828797?text=Halo%20PAS%20Market%2C%20saya%20ingin%20registrasi%20customer%20baru" class="auth-contact auth-contact--primary" target="_blank" rel="noopener">
                        <span class="auth-contact-icon"><i class="bi bi-whatsapp"></i></span>
                        <span class="auth-contact-body">
                            <span class="auth-contact-label">WhatsApp</span>
                            <span class="auth-contact-value">085-101-828-797</span>
                        </span>
                        <i class="bi bi-arrow-right auth-contact-arrow"></i>
                    </a>
                    <!-- <a href="tel:+622112345678" class="auth-contact">
                        <span class="auth-contact-icon auth-contact-icon--phone"><i class="bi bi-telephone"></i></span>
                        <span class="auth-contact-body">
                            <span class="auth-contact-label">Telepon</span>
                            <span class="auth-contact-value">085-101-828-797</span>
                        </span>
                        <i class="bi bi-arrow-right auth-contact-arrow"></i>
                    </a> -->
                </div>

                <div class="auth-switch">
                    Sudah punya akun? <a href="{{ $loginUrl }}">Login disini</a>
                </div>

                <div class="auth-secure">
                    <i class="bi bi-shield-check"></i>
                    Data Anda aman dengan enkripsi SSL 256-bit
                </div>
            </div>
        </main>
    </div>
</section>

<!-- ====================== MOBILE REGISTER INFO ====================== -->
<section class="d-lg-none" id="mobRegisterInfo">
    <div class="login-mob-inner">
        <div class="login-mob-logo-wrap">
            <div class="login-mob-logo-box">
                <i class="bi bi-person-check"></i>
                <span>PAS</span>
            </div>
        </div>
        <div class="login-mob-head">
            <h2 class="login-mob-title">Registrasi Customer Baru</h2>
            <p class="login-mob-desc">Silakan hubungi Sales / Admin kami</p>
        </div>

        <div class="mobri-notice">
            <i class="bi bi-info-circle"></i>
            <span>Pendaftaran tidak dapat dilakukan secara mandiri — tim Sales/Admin kami yang akan membuatkan akun Anda.</span>
        </div>

        <div class="mobri-label">Hubungi Kami</div>
        <div class="mobri-contacts">
            <a href="https://wa.me/6285101828797?text=Halo%20PAS%20Market%2C%20saya%20ingin%20registrasi%20customer%20baru" class="mobri-btn mobri-btn--wa" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i>
                <span>WhatsApp</span>
            </a>
            <a href="tel:+622112345678" class="mobri-btn">
                <i class="bi bi-telephone"></i>
                <span>Telepon</span>
            </a>
        </div>
    </div>

    <div class="login-mob-register">
        Sudah punya akun? <a href="{{ $loginUrl }}">Login</a>
    </div>
</section>
@endsection
