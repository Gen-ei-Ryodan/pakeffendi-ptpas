@extends('admin.layouts.app')

@section('title', 'Customer')
@section('breadcrumb', 'Home / Customer / List')
@section('header', 'Manage Customer')

@section('content')
{{-- Stats Cards --}}
@php
    $totalCount = $customers->total();
    $newCount = \App\Models\Customer::where('status', 'new')->count();
    $activeCount = \App\Models\Customer::where('status', 'active')->count();
    $blacklistCount = \App\Models\Customer::where('status', 'blacklist')->count();
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
            <div class="text-2xl font-bold text-amber-700">{{ $newCount }}</div>
            <div class="text-xs text-slate-500">New</div>
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
        <div class="w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div>
            <div class="text-2xl font-bold text-rose-700">{{ $blacklistCount }}</div>
            <div class="text-xs text-slate-500">Blacklist</div>
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
                <a href="{{ route('admin.customers.index', ['status' => 'new']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                   {{ $status === 'new' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        New
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
                <a href="{{ route('admin.customers.index', ['status' => 'blacklist']) }}"
                   class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                   {{ $status === 'blacklist' ? 'bg-rose-100 text-rose-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                    <span class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Blacklist
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
                                'new' => ['bg-amber-100', 'text-amber-700', 'bg-amber-500'],
                                'active' => ['bg-emerald-100', 'text-emerald-700', 'bg-emerald-500'],
                                'blacklist' => ['bg-rose-100', 'text-rose-700', 'bg-rose-500'],
                                'pending' => ['bg-amber-100', 'text-amber-700', 'bg-amber-500'],
                                'rejected' => ['bg-slate-100', 'text-slate-700', 'bg-slate-500'],
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
                            @if($customer->status === 'new' || $customer->status === 'pending')
                                <button type="button" onclick="openApproveModal({{ $customer->id }}, '{{ $customer->full_name }}')" class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Approve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <form method="post" action="{{ route('admin.customers.reject', $customer) }}" class="inline" onsubmit="return confirm('Tolak customer {{ $customer->full_name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition-colors" title="Reject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($customer->status === 'active')
                                <form method="post" action="{{ route('admin.customers.blacklist', $customer) }}" class="inline" onsubmit="return confirm('Blacklist customer {{ $customer->full_name }}? Customer tidak akan bisa melakukan order.')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition-colors" title="Blacklist">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    </button>
                                </form>
                            @endif
                            @if($customer->status === 'blacklist')
                                <form method="post" action="{{ route('admin.customers.unblacklist', $customer) }}" class="inline" onsubmit="return confirm('Hapus blacklist untuk customer {{ $customer->full_name }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Remove Blacklist">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.customers.show', $customer) }}"
                               class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-sky-600 transition-colors" title="Lihat">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
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

{{-- Modal Approve Customer --}}
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-800">Approve Customer</h3>
        </div>
        <form id="approveForm" method="POST" action="">
            @csrf
            @method('PATCH')
            <div class="px-6 py-4">
                <p class="text-sm text-slate-600 mb-4">
                    Anda akan menyetujui customer: <strong id="customerName"></strong>
                </p>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Internal Customer ID <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="internal_code" required
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-shadow"
                           placeholder="Masukkan Internal Customer ID">
                    <p class="text-xs text-slate-500 mt-1">Wajib diisi untuk mengaktifkan customer</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
                <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition-colors">
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(customerId, customerName) {
    const modal = document.getElementById('approveModal');
    const form = document.getElementById('approveForm');
    const nameElement = document.getElementById('customerName');
    
    form.action = '/admin/customers/' + customerId + '/approve';
    nameElement.textContent = customerName;
    modal.classList.remove('hidden');
}

function closeApproveModal() {
    const modal = document.getElementById('approveModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeApproveModal();
    }
});
</script>
@endsection
