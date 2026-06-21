@extends('admin.layouts.app')

@section('title', 'Create Customer')
@section('breadcrumb', 'Home / Customer / Create')
@section('header', 'Create Customer')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Form Tambah Customer Baru</h2>
                    <p class="text-sm text-white/80">Lengkapi semua data customer dengan benar</p>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('admin.customers.store') }}" class="p-6 space-y-8">
            @csrf

            {{-- Section 1: Akun & Identitas --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-sky-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Akun & Identitas</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input name="full_name" value="{{ old('full_name') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Contoh: John Doe">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Jenis Akun <span class="text-red-500">*</span></label>
                        <select name="account_type" required
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow">
                            <option value="personal" @selected(old('account_type') === 'personal')>Personal</option>
                            <option value="company" @selected(old('account_type') === 'company')>Company</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No KTP <span class="text-red-500">*</span></label>
                        <input name="ktp_number" value="{{ old('ktp_number') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="16 digit nomor KTP">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">NPWP</label>
                        <input name="npwp" value="{{ old('npwp') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Nomor NPWP (opsional)">
                    </div>
                </div>
            </div>

            {{-- Section 2: Login --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Login</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="contoh@email.com">
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow"
                               placeholder="Minimal 8 karakter">
                        <p class="text-xs text-slate-400 mt-1">Minimal 8 karakter, kombinasi huruf dan angka</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Konfirmasi Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow"
                               placeholder="Ulangi password">
                    </div>
                </div>
            </div>

            {{-- Section 3: Alamat --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Alamat</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                        <textarea name="address" rows="3"
                                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                                  placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Provinsi</label>
                        <input name="province" value="{{ old('province') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Nama provinsi">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kota</label>
                        <input name="city" value="{{ old('city') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Nama kota">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kode POS</label>
                        <input name="postal_code" value="{{ old('postal_code') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Kode POS">
                    </div>
                </div>
            </div>

            {{-- Section 4: Kontak & Perusahaan --}}
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Kontak & Perusahaan</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No HP <span class="text-red-500">*</span></label>
                        <input name="phone" value="{{ old('phone') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="08xxxxxxxxxx">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Contact Person <span class="text-red-500">*</span></label>
                        <input name="contact_person" value="{{ old('contact_person') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Nama contact person">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Perusahaan</label>
                        <input name="company_name" value="{{ old('company_name') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Nama perusahaan (opsional)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Internal Code</label>
                        <input name="internal_code" value="{{ old('internal_code') }}"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400"
                               placeholder="Kode internal (opsional)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Sales Person</label>
                        <select name="sales_id"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow">
                            <option value="">-- Tanpa Sales --</option>
                            @foreach($sales as $s)
                                <option value="{{ $s->id }}" @selected(old('sales_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-400 mt-1">Kosongkan jika customer mendaftar sendiri / tanpa sales.</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.customers.index') }}"
                   class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 transition-all shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Simpan Customer
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
