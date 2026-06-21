<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Mail\BuyerVerificationMail;
use App\Mail\ChangePasswordMail;
use App\Models\Customer;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function showLogin(Request $request)
    {
        if ($request->filled('redirect')) {
            $request->session()->put('url.intended', $request->string('redirect')->toString());
        }

        return view('guest.auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:190'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $login = trim((string) $validated['login']);
        $credentials = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? ['email' => $login, 'password' => $validated['password']]
            : ['phone' => $login, 'password' => $validated['password']];

        $remember = (bool) ($validated['remember'] ?? false);

        // 1. Try Customer Login
        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $customer = Auth::guard('customer')->user();

            if ($customer->status !== 'active') {
                Auth::guard('customer')->logout();

                return back()->withErrors(['login' => 'Akun Anda belum aktif atau ditolak. Silakan hubungi Admin.']);
            }

            // Check if email is verified
            if (is_null($customer->email_verified_at)) {
                Auth::guard('customer')->logout();

                // Resend verification code
                $this->sendVerificationCode($customer);

                return redirect()->route('guest.verify-email', ['email' => $customer->email, 'mode' => 'login'])
                    ->with('info', 'Silakan verifikasi email Anda terlebih dahulu. Kode verifikasi telah dikirim ke email Anda.');
            }

            $request->session()->regenerate();
            $resolved = $this->cartService->resolve($request, $customer);

            return redirect()->intended('/profile')
                ->withCookie($resolved['cookie'])
                ->with('success', 'Login berhasil! Selamat datang kembali, '.$customer->full_name);
        }

        // 2. Try Sales/Admin Login (User guard)
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            if (Auth::guard('web')->attempt(['email' => $login, 'password' => $validated['password']], $remember)) {
                $user = Auth::guard('web')->user();

                if ($user->role === 'sales') {
                    $request->session()->regenerate();

                    return redirect()->intended('/profile')
                        ->with('success', 'Login berhasil sebagai Sales! Selamat datang kembali, '.$user->name);
                }

                Auth::guard('web')->logout();
            }
        }

        return back()
            ->withErrors(['login' => 'Email/HP atau kata sandi salah.'])
            ->onlyInput('login');
    }

    // ──────────────────────────────────────────────
    // REGISTER BUYER (by Sales / Admin)
    // Data disimpan di session dulu, baru ke DB setelah OTP diverifikasi
    // ──────────────────────────────────────────────

    public function showRegisterBuyer()
    {
        $user = Auth::guard('web')->user();
        if (!$user || !($user->isSales() || $user->isAdmin())) {
            return redirect()->route('guest.profile.index')->with('error', 'Akses ditolak.');
        }

        return view('guest.auth.register-buyer');
    }

    public function registerBuyer(Request $request)
    {
        $user = Auth::guard('web')->user();
        if (!$user || !($user->isSales() || $user->isAdmin())) {
            return redirect()->route('guest.profile.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:customers,email'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'address' => ['nullable', 'string', 'max:500'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $code = $this->generateVerificationCode();

        // Simpan data ke session — belum ke DB
        session()->put('pending_buyer', [
            'sales_id' => $user->id,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'province' => $validated['province'] ?? null,
            'city' => $validated['city'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'password' => Hash::make($validated['password']),
            'verification_code' => $code,
            'otp_expires_at' => now()->addMinute()->timestamp,
        ]);

        // Kirim OTP
        try {
            $tempCustomer = new Customer();
            $tempCustomer->full_name = $validated['full_name'];
            $tempCustomer->email = $validated['email'];

            Mail::to($validated['email'])->send(new BuyerVerificationMail($tempCustomer, $code));
        } catch (\Exception $e) {
            Log::error('Verification Mail Failed: '.$e->getMessage());
        }

        return redirect()->route('guest.verify-email', ['email' => $validated['email'], 'mode' => 'register'])
            ->with('info', 'Kode verifikasi telah dikirim ke email '.$validated['email'].'. Silakan verifikasi untuk menyelesaikan pendaftaran.');
    }

    // ──────────────────────────────────────────────
    // EMAIL VERIFICATION
    // ──────────────────────────────────────────────

    public function showVerifyEmail(Request $request)
    {
        $email = $request->query('email', '');
        $mode = $request->query('mode', ''); // 'register' or 'login'
        $otpExpiresAt = null;

        // Get OTP expiry from session if pending
        $pending = session('pending_buyer');
        if ($pending && $pending['email'] === $email) {
            $otpExpiresAt = $pending['otp_expires_at'] ?? null;
        }

        return view('guest.auth.verify-email', compact('email', 'mode', 'otpExpiresAt'));
    }

    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        // Cek apakah ini dari pendaftaran baru (data di session)
        $pending = session('pending_buyer');
        if ($pending && $pending['email'] === $validated['email']) {
            // Verify OTP
            if ($pending['verification_code'] !== $validated['code']) {
                return back()->withErrors(['code' => 'Kode verifikasi tidak valid.'])->withInput();
            }

            // OTP valid — simpan ke DB
            $customer = Customer::create([
                'customer_code' => $this->generateCustomerCode(),
                'full_name' => $pending['full_name'],
                'email' => $pending['email'],
                'phone' => $pending['phone'],
                'address' => $pending['address'],
                'province' => $pending['province'] ?? null,
                'city' => $pending['city'] ?? null,
                'postal_code' => $pending['postal_code'] ?? null,
                'company_name' => $pending['company_name'],
                'password' => $pending['password'],
                'status' => 'pending',
                'sales_id' => $pending['sales_id'],
                'account_type' => 'personal',
                'ktp_number' => '-',
                'contact_person' => $pending['full_name'],
                'email_verified_at' => now(),
            ]);

            session()->forget('pending_buyer');

            return redirect()->route('guest.profile.my-customers.index')
                ->with('success', 'Akun buyer '.$customer->full_name.' berhasil dibuat dan email sudah diverifikasi! Menunggu persetujuan Admin.');
        }

        // Jika bukan dari pending (misal buyer yang sudah ada tapi belum verifikasi)
        $customer = Customer::query()
            ->where('email', $validated['email'])
            ->where('email_verification_code', $validated['code'])
            ->first();

        if (!$customer) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.'])->withInput();
        }

        $customer->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);

        if (Auth::guard('web')->check() && (Auth::guard('web')->user()->isSales() || Auth::guard('web')->user()->isAdmin())) {
            return redirect()->route('guest.profile.my-customers.index')
                ->with('success', 'Email buyer berhasil diverifikasi! Buyer sudah bisa login.');
        }

        return redirect()->route('guest.login')
            ->with('success', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function verifyEmailDirect(string $code)
    {
        // Cek session pending dulu
        $pending = session('pending_buyer');
        if ($pending && $pending['verification_code'] === $code) {
            $customer = Customer::create([
                'customer_code' => $this->generateCustomerCode(),
                'full_name' => $pending['full_name'],
                'email' => $pending['email'],
                'phone' => $pending['phone'],
                'address' => $pending['address'],
                'province' => $pending['province'] ?? null,
                'city' => $pending['city'] ?? null,
                'postal_code' => $pending['postal_code'] ?? null,
                'company_name' => $pending['company_name'],
                'password' => $pending['password'],
                'status' => 'pending',
                'sales_id' => $pending['sales_id'],
                'account_type' => 'personal',
                'ktp_number' => '-',
                'contact_person' => $pending['full_name'],
                'email_verified_at' => now(),
            ]);

            session()->forget('pending_buyer');

            return redirect()->route('guest.profile.my-customers.index')
                ->with('success', 'Akun buyer '.$customer->full_name.' berhasil dibuat dan email sudah diverifikasi! Menunggu persetujuan Admin.');
        }

        // Cek customer yang sudah ada
        $customer = Customer::query()
            ->where('email_verification_code', $code)
            ->first();

        if (!$customer) {
            return redirect()->route('guest.login')
                ->with('error', 'Kode verifikasi tidak valid atau sudah kadaluarsa.');
        }

        $customer->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ]);

        if (Auth::guard('web')->check() && (Auth::guard('web')->user()->isSales() || Auth::guard('web')->user()->isAdmin())) {
            return redirect()->route('guest.profile.my-customers.index')
                ->with('success', 'Email buyer berhasil diverifikasi! Buyer sudah bisa login.');
        }

        return redirect()->route('guest.login')
            ->with('success', 'Email berhasil diverifikasi! Silakan login.');
    }

    public function resendVerificationCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:190'],
        ]);

        $email = $validated['email'];

        // 1. Cek apakah ada pending registration di session
        $pending = session('pending_buyer');
        if ($pending && $pending['email'] === $email) {
            $code = $this->generateVerificationCode();
            $pending['verification_code'] = $code;
            $pending['otp_expires_at'] = now()->addMinute()->timestamp;
            session()->put('pending_buyer', $pending);

            try {
                $tempCustomer = new Customer();
                $tempCustomer->full_name = $pending['full_name'];
                $tempCustomer->email = $email;
                Mail::to($email)->send(new BuyerVerificationMail($tempCustomer, $code));
            } catch (\Exception $e) {
                Log::error('Resend OTP Failed: '.$e->getMessage());
            }

            return back()->with('info', 'Kode verifikasi baru telah dikirim ke email '.$email.'.');
        }

        // 2. Cek customer yang sudah ada tapi belum verifikasi
        $customer = Customer::query()->where('email', $email)->whereNull('email_verified_at')->first();
        if ($customer) {
            $this->sendVerificationCode($customer);
            return back()->with('info', 'Kode verifikasi baru telah dikirim ke email '.$email.'.');
        }

        return back()->withErrors(['email' => 'Email tidak ditemukan.']);
    }

    private function sendVerificationCode(Customer $customer): void
    {
        $code = $this->generateVerificationCode();
        $customer->update(['email_verification_code' => $code]);

        try {
            Mail::to($customer->email)->send(new BuyerVerificationMail($customer, $code));
        } catch (\Exception $e) {
            Log::error('Resend Verification Mail Failed: '.$e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // CHANGE PASSWORD
    // ──────────────────────────────────────────────

    public function showChangePassword()
    {
        $shopper = $this->getShopper();

        if (!$shopper || $shopper instanceof \App\Models\User) {
            return redirect()->route('guest.profile.index')
                ->with('error', 'Halaman ini hanya untuk pembeli.');
        }

        $hasVerifiedEmail = !is_null($shopper->email_verified_at);

        return view('guest.auth.change-password', [
            'customer' => $shopper,
            'hasVerifiedEmail' => $hasVerifiedEmail,
            'step' => session('change_password_step', 'send_code'),
        ]);
    }

    public function sendChangePasswordCode(Request $request)
    {
        $shopper = $this->getShopper();

        if (!$shopper || $shopper instanceof \App\Models\User) {
            return redirect()->route('guest.profile.index')->with('error', 'Akses ditolak.');
        }

        $code = $this->generateVerificationCode();
        $shopper->update(['email_verification_code' => $code]);

        try {
            Mail::to($shopper->email)->send(new ChangePasswordMail($shopper, $code));
        } catch (\Exception $e) {
            Log::error('Change Password Mail Failed: '.$e->getMessage());
        }

        session()->put('change_password_step', 'verify');
        session()->put('change_password_email', $shopper->email);

        return redirect()->route('guest.change-password')
            ->with('success', 'Kode verifikasi telah dikirim ke email '.$shopper->email);
    }

    public function changePassword(Request $request)
    {
        $shopper = $this->getShopper();

        if (!$shopper || $shopper instanceof \App\Models\User) {
            return redirect()->route('guest.profile.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($shopper->email_verification_code !== $validated['code']) {
            return back()->withErrors(['code' => 'Kode verifikasi tidak valid.'])->withInput();
        }

        $shopper->update([
            'password' => Hash::make($validated['password']),
            'email_verification_code' => null,
            'email_verified_at' => now(),
        ]);

        session()->forget(['change_password_step', 'change_password_email']);

        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guest.login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }

    // ──────────────────────────────────────────────
    // LOGOUT
    // ──────────────────────────────────────────────

    public function logout(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        }

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user->role === 'sales') {
                Auth::guard('web')->logout();
            }
        }

        if (! Auth::guard('customer')->check() && ! Auth::guard('web')->check()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->to('/')->with('success', 'Anda telah berhasil logout.');
    }

    // ──────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────

    private function getShopper()
    {
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales()) {
            return Auth::guard('web')->user();
        }
        return null;
    }

    private function generateVerificationCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generateCustomerCode(): string
    {
        return 'W'.now()->format('ym').str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
