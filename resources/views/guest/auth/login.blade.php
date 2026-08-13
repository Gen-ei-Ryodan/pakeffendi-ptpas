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
<!-- ====================== DESKTOP LOGIN (split-screen) ====================== -->
<section class="auth-desktop mobile-hide" id="desktopLogin">
    <div class="auth-shell">
        <aside class="auth-aside">
            <a href="{{ url('/') }}" class="auth-aside-brand">
                <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
            </a>
            <div class="auth-aside-body">
                <span class="auth-aside-chip">Trading Floor B2B</span>
                <h2 class="auth-aside-title">Belanja grosir jadi <em>lebih mudah dan terpercaya.</em></h2>
                <p class="auth-aside-desc">Harga bertingkat, stok real-time, dan pengiriman ke seluruh Indonesia — semua dalam satu platform.</p>
                <ul class="auth-aside-points">
                    <li><i class="bi bi-check-circle-fill"></i> Harga grosir bertingkat</li>
                    <li><i class="bi bi-check-circle-fill"></i> Stok real-time dari pusat</li>
                    <li><i class="bi bi-check-circle-fill"></i> Pengiriman ke seluruh Indonesia</li>
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
                    <div class="auth-brand-mark"><i class="bi bi-shop"></i></div>
                    <h1 class="auth-title">Selamat Datang</h1>
                    <p class="auth-sub">Masuk untuk melanjutkan belanja grosir Anda</p>
                </div>

                @if($errors->any())
                <div class="auth-alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <div>
                        @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('guest.login.store') }}" data-ajax="false" novalidate id="loginForm">
                    @csrf
                    <div class="auth-field">
                        <label for="email" class="auth-label">Email atau Nomor HP</label>
                        <div class="auth-input">
                            <i class="bi bi-envelope"></i>
                            <input type="text" class="form-control" id="email" name="login" value="{{ old('login') }}" placeholder="nama@email.com" autocomplete="username" required>
                        </div>
                    </div>

                    <div class="auth-field">
                        <div class="auth-label-row">
                            <label for="password" class="auth-label">Kata Sandi</label>
                            <a href="#" class="auth-forgot">Lupa password?</a>
                        </div>
                        <div class="auth-input">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                            <button type="button" class="auth-eye" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="auth-remember">
                        <label>
                            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                            <span class="auth-check">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit" class="auth-btn">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login</span>
                    </button>
                </form>

                <div class="auth-switch">
                    Belum punya akun? <a href="{{ url('/register') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}">Daftar sekarang</a>
                </div>

                <div class="auth-secure">
                    <i class="bi bi-shield-check"></i>
                    Transaksi Anda aman dengan enkripsi SSL 256-bit
                </div>
            </div>
        </main>
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

            <div class="login-mob-remember">
                <label class="login-mob-remember-label">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="login-mob-forgot-link">Lupa password?</a>
            </div>

            <button type="submit" class="login-mob-btn" id="mobLoginBtn">Masuk</button>
        </form>

        <p class="login-mob-terms">Dengan masuk, Anda menyetujui <a href="#">Syarat &amp; Ketentuan</a></p>
    </div>

    <div class="login-mob-register" style="visibility:hidden;">
        &nbsp;
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
    setupPwdToggle('mobTogglePassword', 'mobPassword');

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
            const label = btn ? btn.querySelector('span') : null;
            if (!login.value.trim()) { e.preventDefault(); alert('Mohon isi email atau nomor HP'); login.focus(); return; }
            if (!password.value.trim()) { e.preventDefault(); alert('Mohon isi kata sandi'); password.focus(); return; }
            if (btn) {
                btn.disabled = true;
                if (label) label.textContent = 'Memproses...';
            }
        });
    }
});
</script>
@endpush
