@extends('guest.layouts.app')

@section('title', 'Daftar - PAS Market')

@push('styles')
    <!-- Custom CSS -->
    <link href="{{ asset('guest/css/auth.css') }}" rel="stylesheet">
@endpush

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daftar</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Register Section -->
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
                            <p class="text-muted mt-2">Buat akun baru untuk memulai belanja</p>
                        </div>
                        
                        <!-- Register Form -->
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
                        
                        <!-- Login Link -->
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
                
                <!-- Security Notice -->
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const submitBtn = registerForm?.querySelector('button[type="submit"]');
    
    // Toggle password visibility
    function setupPasswordToggle(toggleId, inputId) {
        const toggleBtn = document.getElementById(toggleId);
        const passwordInput = document.getElementById(inputId);
        
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                const icon = this.querySelector('i');
                icon.classList.toggle('bi-eye');
                icon.classList.toggle('bi-eye-slash');
            });
        }
    }
    
    setupPasswordToggle('togglePassword', 'password');
    setupPasswordToggle('toggleConfirmPassword', 'confirmPassword');
    
    // Form validation and submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const fullNameInput = document.getElementById('fullName');
            const emailInput = document.getElementById('email');
            const phoneInput = document.getElementById('phone');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const termsCheckbox = document.getElementById('terms');
            
            // Basic validation
            if (!fullNameInput.value.trim()) {
                e.preventDefault();
                alert('Mohon isi nama lengkap');
                fullNameInput.focus();
                return;
            }
            
            if (!emailInput.value.trim()) {
                e.preventDefault();
                alert('Mohon isi email');
                emailInput.focus();
                return;
            }
            
            if (!phoneInput.value.trim()) {
                e.preventDefault();
                alert('Mohon isi nomor HP');
                phoneInput.focus();
                return;
            }
            
            if (passwordInput.value.length < 8) {
                e.preventDefault();
                alert('Kata sandi minimal 8 karakter');
                passwordInput.focus();
                return;
            }
            
            if (passwordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('Konfirmasi kata sandi tidak cocok');
                confirmPasswordInput.focus();
                return;
            }
            
            if (!termsCheckbox.checked) {
                e.preventDefault();
                alert('Mohon setujui syarat dan ketentuan');
                termsCheckbox.focus();
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
