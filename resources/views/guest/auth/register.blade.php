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
<!-- ====================== DESKTOP REGISTER (split-screen) ====================== -->
<section class="auth-desktop mobile-hide" id="desktopRegister">
    <div class="auth-shell">
        <aside class="auth-aside">
            <a href="{{ url('/') }}" class="auth-aside-brand">
                <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
            </a>
            <div class="auth-aside-body">
                <span class="auth-aside-chip">Trading Floor B2B</span>
                <h2 class="auth-aside-title">Mulai belanja grosir <em>dengan satu akun.</em></h2>
                <p class="auth-aside-desc">Akses harga bertingkat, pantau stok real-time, dan kelola pesanan dalam satu platform.</p>
                <ul class="auth-aside-points">
                    <li><i class="bi bi-check-circle-fill"></i> Daftar gratis, tanpa biaya</li>
                    <li><i class="bi bi-check-circle-fill"></i> Harga grosir khusus buyer</li>
                    <li><i class="bi bi-check-circle-fill"></i> Layani pelanggan Anda sendiri</li>
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
                    <div class="auth-brand-mark"><i class="bi bi-person-plus"></i></div>
                    <h1 class="auth-title">Buat Akun Baru</h1>
                    <p class="auth-sub">Daftar gratis untuk mulai belanja grosir</p>
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

                <form method="POST" action="{{ route('guest.register.store') }}" data-ajax="false" novalidate id="registerForm">
                    @csrf
                    <div class="auth-field">
                        <label for="fullName" class="auth-label">Nama Lengkap</label>
                        <div class="auth-input">
                            <i class="bi bi-person"></i>
                            <input type="text" class="form-control" id="fullName" name="full_name" value="{{ old('full_name') }}" placeholder="Nama lengkap" required>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="email" class="auth-label">Email</label>
                        <div class="auth-input">
                            <i class="bi bi-envelope"></i>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="phone" class="auth-label">Nomor HP</label>
                        <div class="auth-input">
                            <i class="bi bi-phone"></i>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="address" class="auth-label">Alamat (opsional)</label>
                        <div class="auth-input">
                            <i class="bi bi-geo-alt"></i>
                            <textarea class="form-control" id="address" name="address" rows="2" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="password" class="auth-label">Kata Sandi</label>
                        <div class="auth-input">
                            <i class="bi bi-lock"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" autocomplete="new-password" required>
                            <button type="button" class="auth-eye" id="togglePassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <small class="auth-hint">Minimal 8 karakter</small>
                    </div>
                    <div class="auth-field">
                        <label for="confirmPassword" class="auth-label">Konfirmasi Kata Sandi</label>
                        <div class="auth-input">
                            <i class="bi bi-shield-lock"></i>
                            <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" placeholder="Ulangi kata sandi" autocomplete="new-password" required>
                            <button type="button" class="auth-eye" id="toggleConfirmPassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <label class="auth-terms">
                        <input type="checkbox" id="terms" name="terms" value="1" required>
                        <span>Saya menyetujui <a href="#">Syarat &amp; Ketentuan</a></span>
                    </label>

                    <button type="submit" class="auth-btn">
                        <i class="bi bi-person-plus"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </form>

                <div class="auth-switch">
                    Sudah punya akun? <a href="{{ url('/login') }}{{ request('redirect') ? ('?redirect=' . urlencode(request('redirect'))) : '' }}">Login disini</a>
                </div>

                <div class="auth-secure">
                    <i class="bi bi-shield-check"></i>
                    Data Anda aman dengan enkripsi SSL 256-bit
                </div>
            </div>
        </main>
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
