@extends('admin.layouts.app')

@section('title', 'Permintaan Perubahan Customer')
@section('breadcrumb', 'Home / Customer / Permintaan Perubahan')
@section('header', 'Permintaan Perubahan Customer')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-white">Daftar Permintaan Perubahan</h2>
                <p class="text-xs text-white/80">Review perubahan data customer dari sales</p>
            </div>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/50">
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('admin.customers.change-requests.index') }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
               {{ !request('status') ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Semua
                </span>
            </a>
            <a href="{{ route('admin.customers.change-requests.index', ['status' => 'pending']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
               {{ request('status') === 'pending' ? 'bg-amber-100 text-amber-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                </span>
            </a>
            <a href="{{ route('admin.customers.change-requests.index', ['status' => 'approved']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
               {{ request('status') === 'approved' ? 'bg-emerald-100 text-emerald-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                </span>
            </a>
            <a href="{{ route('admin.customers.change-requests.index', ['status' => 'rejected']) }}"
               class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
               {{ request('status') === 'rejected' ? 'bg-rose-100 text-rose-700 shadow-sm' : 'text-slate-500 hover:bg-slate-100' }}">
                <span class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rejected
                </span>
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('status'))
        <div class="mx-6 mt-4 px-4 py-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mx-6 mt-4 px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4">ID</th>
                    <th class="py-3.5 px-4">Customer</th>
                    <th class="py-3.5 px-4">Sales</th>
                    <th class="py-3.5 px-4">Perubahan</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4">Tanggal</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($changeRequests as $cr)
                @php
                    $fieldCount = count($cr->changes ?? []);
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-3.5 px-4 text-xs text-slate-500">#{{ $cr->id }}</td>
                    <td class="py-3.5 px-4">
                        <div class="font-semibold text-slate-800">{{ $cr->customer?->full_name }}</div>
                        <div class="text-xs text-slate-400">{{ $cr->customer?->customer_code }}</div>
                    </td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $cr->sales?->name }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($cr->changes as $field => $value)
                                <span class="inline-block px-2 py-0.5 rounded-md text-xs font-medium bg-sky-50 text-sky-700 border border-sky-100">{{ str_replace('_', ' ', $field) }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3.5 px-4">
                        @php
                            $badge = match($cr->status) {
                                'pending' => ['bg-amber-100', 'text-amber-700', 'bg-amber-500'],
                                'approved' => ['bg-emerald-100', 'text-emerald-700', 'bg-emerald-500'],
                                'rejected' => ['bg-rose-100', 'text-rose-700', 'bg-rose-500'],
                                default => ['bg-slate-100', 'text-slate-700', 'bg-slate-500'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badge[0] }} {{ $badge[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $badge[2] }}"></span>
                            {{ ucfirst($cr->status) }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-xs text-slate-500">{{ $cr->created_at->format('d M Y H:i') }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.customers.change-requests.show', $cr) }}"
                               class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600 transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($cr->status === 'pending')
                            <form method="post" action="{{ route('admin.customers.change-requests.approve', $cr) }}" class="inline" onsubmit="return confirm('Setujui perubahan? Data customer akan langsung diupdate.')">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Approve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </form>
                            <button type="button" id="rejectBtn{{ $cr->id }}" class="p-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 transition-colors" title="Reject" onclick="showReject({{ $cr->id }})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Belum ada permintaan perubahan</div>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($changeRequests->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div class="text-sm text-slate-500">
            Menampilkan <span class="font-medium text-slate-700">{{ $changeRequests->firstItem() ?? 0 }}</span>
            -
            <span class="font-medium text-slate-700">{{ $changeRequests->lastItem() ?? 0 }}</span>
            dari <span class="font-medium text-slate-700">{{ $changeRequests->total() }}</span> data
        </div>
        <div>{{ $changeRequests->links() }}</div>
    </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectReason" class="fixed inset-0 bg-black/50 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">Tolak Perubahan</h3>
        </div>
        <div class="px-6 py-4">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Penolakan (opsional)</label>
            <textarea id="rejectText" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition-shadow" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
        </div>
        <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
            <button onclick="closeReject()" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
            <button onclick="submitReject()" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 transition-colors">Tolak</button>
        </div>
    </div>
</div>

<script>
let currentRejectId = null;

function showReject(id) {
    currentRejectId = id;
    document.getElementById('rejectText').value = '';
    var el = document.getElementById('rejectReason');
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function closeReject() {
    var el = document.getElementById('rejectReason');
    el.classList.add('hidden');
    el.classList.remove('flex');
    currentRejectId = null;
}

function submitReject() {
    if (!currentRejectId) return;
    var reason = document.getElementById('rejectText').value;
    var url = '{{ url("/admin/change-requests") }}/' + currentRejectId + '/reject';
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.innerHTML = '<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PATCH"><input type="hidden" name="rejection_reason" value="'+reason.replace(/"/g,'&quot;')+'">';
    document.body.appendChild(form);
    form.submit();
}

// Close on overlay click
document.getElementById('rejectReason').addEventListener('click', closeReject);
</script>
@endsection
