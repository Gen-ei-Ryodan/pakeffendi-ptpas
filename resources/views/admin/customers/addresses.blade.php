@extends('admin.layouts.app')

@section('title', 'Alamat Customer')
@section('breadcrumb', 'Home / Customer / Alamat')
@section('header', 'Kelola Alamat Customer')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Customer --}}
    <div class="lg:col-span-1">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-5 py-4">
                <h3 class="text-sm font-semibold text-white">Informasi Customer</h3>
            </div>
            <div class="p-5 space-y-3">
                @php
                    $initial = strtoupper(substr($customer->full_name, 0, 1));
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-lg font-bold">{{ $initial }}</div>
                    <div>
                        <div class="font-semibold text-slate-800">{{ $customer->full_name }}</div>
                        <div class="text-xs text-slate-400">{{ $customer->customer_code }}</div>
                    </div>
                </div>
                <div class="border-t border-slate-100 pt-3 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email</span>
                        <span class="text-slate-700">{{ $customer->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No HP</span>
                        <span class="text-slate-700">{{ $customer->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Alamat</span>
                        <span class="font-semibold text-sky-600">{{ $addresses->count() }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.customers.edit', $customer) }}"
                   class="block text-center py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50 transition-colors mt-2">
                    Kembali ke Edit Customer
                </a>
            </div>
        </div>
    </div>

    {{-- Form Tambah --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Flash Message --}}
        @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-5 py-3 text-sm text-emerald-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
        @endif

        {{-- Form Tambah Alamat --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 py-4">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Alamat Baru
                </h3>
            </div>
            <form method="post" action="{{ route('admin.customers.addresses.store', $customer) }}" class="p-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Label Alamat</label>
                        <input name="label" placeholder="Contoh: Rumah, Kantor" value="{{ old('label') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Penerima</label>
                        <input name="recipient_name" placeholder="Nama penerima" value="{{ old('recipient_name', $customer->full_name) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">No HP Penerima</label>
                        <input name="phone" placeholder="08xxxxxxxxxx" value="{{ old('phone', $customer->phone) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kode POS</label>
                        <input name="postal_code" placeholder="Kode POS" value="{{ old('postal_code') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Provinsi</label>
                        <input name="province" placeholder="Nama provinsi" value="{{ old('province') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Kota / Kabupaten</label>
                        <input name="city" placeholder="Nama kota" value="{{ old('city') }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="2" required placeholder="Jalan, nomor, RT/RW, kelurahan/desa, kecamatan"
                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">{{ old('address') }}</textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" id="is_active_new" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked(old('is_active', $addresses->isEmpty()))>
                        <label for="is_active_new" class="text-sm text-slate-700">Jadikan alamat utama</label>
                    </div>
                </div>
                <button type="submit"
                        class="px-5 py-2 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 transition-all shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Tambah Alamat
                    </div>
                </button>
            </form>
        </div>

        {{-- Daftar Alamat --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-5 py-4">
                <h3 class="text-sm font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Daftar Alamat ({{ $addresses->count() }})
                </h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($addresses as $addr)
                <div class="p-5 {{ $addr->is_active ? 'bg-sky-50/30' : '' }}" id="address-{{ $addr->id }}">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center gap-2">
                            @if($addr->label)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">{{ $addr->label }}</span>
                            @endif
                            @if($addr->is_active)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Utama
                            </span>
                            @else
                            <form method="post" action="{{ route('admin.customers.addresses.set-active', [$customer, $addr]) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 hover:bg-sky-100 hover:text-sky-600 transition-colors">
                                    Jadikan Utama
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="toggleEdit({{ $addr->id }})"
                                    class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-sky-50 hover:text-sky-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form method="post" action="{{ route('admin.customers.addresses.destroy', [$customer, $addr]) }}" class="inline"
                                  onsubmit="return confirm('Hapus alamat {{ $addr->label ?: 'ini' }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Detail --}}
                    <div class="text-sm text-slate-700 space-y-0.5">
                        <p>{{ $addr->address }}</p>
                        @if($addr->city || $addr->province)
                        <p class="text-slate-500">{{ collect([$addr->city, $addr->province, $addr->postal_code])->filter()->join(', ') }}</p>
                        @endif
                        <p class="text-slate-400 text-xs mt-1">
                            Penerima: {{ $addr->recipient_name ?? $customer->full_name }}
                            @if($addr->phone) &middot; {{ $addr->phone }} @endif
                        </p>
                    </div>

                    {{-- Edit Form --}}
                    <div id="editForm-{{ $addr->id }}" class="mt-4 pt-4 border-t border-slate-100 hidden">
                        <form method="post" action="{{ route('admin.customers.addresses.update', [$customer, $addr]) }}">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Label</label>
                                    <input name="label" value="{{ old('label', $addr->label) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Penerima</label>
                                    <input name="recipient_name" value="{{ old('recipient_name', $addr->recipient_name ?? $customer->full_name) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">No HP</label>
                                    <input name="phone" value="{{ old('phone', $addr->phone ?? $customer->phone) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Kode POS</label>
                                    <input name="postal_code" value="{{ old('postal_code', $addr->postal_code) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Provinsi</label>
                                    <input name="province" value="{{ old('province', $addr->province) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Kota</label>
                                    <input name="city" value="{{ old('city', $addr->city) }}" class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-medium text-slate-600 mb-0.5">Alamat Lengkap</label>
                                    <textarea name="address" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">{{ old('address', $addr->address) }}</textarea>
                                </div>
                                <div class="md:col-span-2 flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" id="is_active_edit_{{ $addr->id }}" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500" @checked($addr->is_active)>
                                    <label for="is_active_edit_{{ $addr->id }}" class="text-sm text-slate-700">Jadikan alamat utama</label>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit" class="px-4 py-1.5 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700 transition-colors">Simpan</button>
                                <button type="button" onclick="toggleEdit({{ $addr->id }})" class="px-4 py-1.5 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="p-10 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-3">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada alamat untuk customer ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleEdit(id) {
    document.getElementById('editForm-' + id).classList.toggle('hidden');
}
</script>
@endpush
