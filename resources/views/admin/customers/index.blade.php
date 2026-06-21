@extends('admin.layouts.app')

@section('title', 'Customer')
@section('breadcrumb', 'Home / Customer / List')
@section('header', 'Manage Customer')

@section('content')
{{-- Stats Cards --}}
@php
    $totalCount = $customers->total();
    $pendingCount = \App\Models\Customer::where('status', 'pending')->count();
    $activeCount = \App\Models\Customer::where('status', 'active')->count();
@endphp
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-sky-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-slate-800">{{ $totalCount }}</div>
            <div class="text-xs text-slate-500">Total Customer</div>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-amber-700">{{ $pendingCount }}</div>
            <div class="text-xs text-slate-500">Pending</div>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-emerald-700">{{ $activeCount }}</div>
            <div class="text-xs text-slate-500">Active</div>
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-purple-700">{{ $totalCount - $pendingCount - $activeCount }}</div>
            <div class="text-xs text-slate-500">Lainnya</div>
        </div>
    </div>
</div>

{{-- Main Card --}}
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">Daftar Customer</h2>
                    <p class="text-xs text-white/80">Kelola semua data customer</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.customers.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sky-700 text-sm font-semibold hover:bg-sky-50 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Customer
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="px-6 py-4 border-b border-slate-100">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('admin.customers.index') }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                   {{ $status === '' ? 'bg-sky-100 text-sky-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        All
                    </span>
                </a>
                <a href="{{ route('admin.customers.index', ['status' => 'pending']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                   {{ $status === 'pending' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Pending
                    </span>
                </a>
                <a href="{{ route('admin.customers.index', ['status' => 'active']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                   {{ $status === 'active' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Active
                    </span>
                </a>
            </div>

            <form method="get" class="flex items-center gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input name="q" value="{{ $q }}" placeholder="Cari customer..."
                           class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow placeholder-slate-400">
                </div>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700 transition-colors">Cari</button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4">Customer</th>
                    <th class="py-3.5 px-4">Kontak</th>
                    <th class="py-3.5 px-4">Alamat</th>
                    <th class="py-3.5 px-4">Sales</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($customers as $customer)
                @php
                    $initial = strtoupper(substr($customer->full_name, 0, 1));
                    $colors = ['bg-sky-100 text-sky-700', 'bg-amber-100 text-amber-700', 'bg-emerald-100 text-emerald-700', 'bg-purple-100 text-purple-700', 'bg-rose-100 text-rose-700', 'bg-cyan-100 text-cyan-700'];
                    $colorIdx = crc32($customer->full_name) % count($colors);
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $colors[$colorIdx] }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ $initial }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $customer->full_name }}</div>
                                <div class="text-xs text-slate-400">{{ $customer->customer_code ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="text-sm text-slate-700">{{ $customer->email }}</div>
                        <div class="text-xs text-slate-400">{{ $customer->phone }}</div>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="text-sm text-slate-700">{{ Str::limit($customer->address, 25) ?: '-' }}</div>
                        <div class="text-xs text-slate-400">{{ $customer->city ?? '-' }}</div>
                    </td>
                    <td class="py-3.5 px-4">
                        @if($customer->sales)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                {{ $customer->sales->name }}
                            </span>
                        @else
                            <span class="text-xs text-slate-400 italic">Mandiri</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4">
                        @php
                            $badge = match($customer->status) {
                                'pending' => ['bg-amber-100', 'text-amber-700', 'bg-amber-500'],
                                'active' => ['bg-emerald-100', 'text-emerald-700', 'bg-emerald-500'],
                                'rejected' => ['bg-rose-100', 'text-rose-700', 'bg-rose-500'],
                                default => ['bg-slate-100', 'text-slate-700', 'bg-slate-500'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $badge[2] }}"></span>
                            {{ ucfirst($customer->status) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($customer->status === 'pending')
                                <form method="post" action="{{ route('admin.customers.approve', $customer) }}" class="inline" onsubmit="return confirm('Setujui customer {{ $customer->full_name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                                <form method="post" action="{{ route('admin.customers.reject', $customer) }}" class="inline" onsubmit="return confirm('Tolak customer {{ $customer->full_name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition-colors" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.customers.edit', $customer) }}"
                               class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-sky-600 transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('admin.customers.addresses.index', $customer) }}"
                               class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600 transition-colors" title="Alamat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </a>
                            <form method="post" action="{{ route('admin.customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Hapus customer {{ $customer->full_name }}? Data tidak bisa dikembalikan.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Belum ada data customer</div>
                            <a href="{{ route('admin.customers.create') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Tambah customer baru</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="text-sm text-slate-500">
            Menampilkan <span class="font-medium text-slate-700">{{ $customers->firstItem() ?? 0 }}</span>
            -
            <span class="font-medium text-slate-700">{{ $customers->lastItem() ?? 0 }}</span>
            dari <span class="font-medium text-slate-700">{{ $customers->total() }}</span> data
        </div>
        <div>{{ $customers->links() }}</div>
    </div>
</div>
@endsection
