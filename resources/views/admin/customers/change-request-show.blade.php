@extends('admin.layouts.app')

@section('title', 'Detail Permintaan Perubahan #'.$changeRequest->id)
@section('breadcrumb', 'Home / Customer / Change Requests / #'.$changeRequest->id)
@section('header', 'Detail Permintaan Perubahan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left: Detail Perubahan --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-white">Perubahan Data</h2>
                        <p class="text-xs text-white/80">{{ $fieldCount }} field perubahan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($changeRequest->status === 'pending')
                    <form method="post" action="{{ route('admin.customers.change-requests.approve', $changeRequest) }}" class="inline" onsubmit="return confirm('Setujui perubahan? Data customer akan langsung diupdate.')">
                        @csrf @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-emerald-700 text-sm font-semibold hover:bg-emerald-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Approve
                        </button>
                    </form>
                    <button onclick="showReject()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-rose-700 text-sm font-semibold hover:bg-rose-50 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Tolak
                    </button>
                    @endif
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                        {{ $changeRequest->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($changeRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700') }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $changeRequest->status === 'pending' ? 'bg-amber-500' : ($changeRequest->status === 'approved' ? 'bg-emerald-500' : 'bg-rose-500') }}"></span>
                        {{ ucfirst($changeRequest->status) }}
                    </span>
                </div>
            </div>

            @if($changeRequest->rejection_reason)
            <div class="mx-6 mt-4 px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <strong>Alasan Penolakan:</strong> {{ $changeRequest->rejection_reason }}
            </div>
            @endif

            <div class="p-6">
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4" style="width:30%">Field</th>
                                <th class="py-3 px-4" style="width:35%">Nilai Saat Ini</th>
                                <th class="py-3 px-4" style="width:35%">Nilai Baru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($changeRequest->changes as $field => $newValue)
                            @php
                                $currentValue = $changeRequest->customer?->$field;
                                $label = ucwords(str_replace('_', ' ', $field));
                                if ($field === 'password') {
                                    $currentValue = '********';
                                    $newValue = '********';
                                }
                                if ($field === 'store_photo_path') {
                                    $label = 'Foto Toko';
                                    $curUrl = $currentValue ? asset('storage/'.$currentValue) : null;
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-3 px-4 font-semibold text-slate-700">{{ $label }}</td>
                                <td class="py-3 px-4 text-slate-500">
                                    @if($field === 'store_photo_path' && $curUrl)
                                        <img src="{{ $curUrl }}" alt="current" class="w-24 h-24 object-cover rounded-lg border">
                                    @else
                                        {{ $currentValue ?: '(kosong)' }}
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-emerald-700 font-semibold">
                                    @if($field === 'store_photo_path' && $newValue)
                                        <img src="{{ asset('storage/'.$newValue) }}" alt="new" class="w-24 h-24 object-cover rounded-lg border border-emerald-300">
                                    @else
                                        {{ $newValue ?: '(kosong)' }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Info Sidebar --}}
    <div class="space-y-4">
        {{-- Customer Info --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Info Customer</h3>
            </div>
            <div class="px-5 py-4">
                @if($changeRequest->customer?->store_photo_path)
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('storage/'.$changeRequest->customer->store_photo_path) }}" alt="Foto Toko" class="w-32 h-32 object-cover rounded-xl border shadow">
                </div>
                @endif
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Nama</span>
                        <span class="font-semibold text-slate-800">{{ $changeRequest->customer?->full_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Kode</span>
                        <span class="text-slate-700">{{ $changeRequest->customer?->customer_code }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Email</span>
                        <span class="text-slate-700">{{ $changeRequest->customer?->email }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">HP</span>
                        <span class="text-slate-700">{{ $changeRequest->customer?->phone }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Status</span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $changeRequest->customer?->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            <span class="w-1 h-1 rounded-full {{ $changeRequest->customer?->status === 'active' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            {{ ucfirst($changeRequest->customer?->status ?? '-') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Request Info --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Info Pengajuan</h3>
            </div>
            <div class="px-5 py-4">
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Diajukan Oleh</span>
                        <span class="font-semibold text-slate-800">{{ $changeRequest->sales?->name }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tanggal</span>
                        <span class="text-slate-700">{{ $changeRequest->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($changeRequest->status !== 'pending')
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Diputuskan Oleh</span>
                        <span class="text-slate-700">{{ $changeRequest->approver?->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Tanggal Keputusan</span>
                        <span class="text-slate-700">{{ $changeRequest->approved_at?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
@if($changeRequest->status === 'pending')
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900">Tolak Permintaan Perubahan</h3>
            <p class="text-sm text-slate-500 mt-1">Customer: {{ $changeRequest->customer?->full_name }}</p>
        </div>
        <form method="post" action="{{ route('admin.customers.change-requests.reject', $changeRequest) }}">
            @csrf @method('PATCH')
            <div class="px-6 py-4">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Alasan Penolakan (opsional)</label>
                <textarea name="rejection_reason" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 transition-shadow" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-3">
                <button type="button" onclick="closeReject()" class="px-4 py-2 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 transition-colors">Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
function showReject() { var el = document.getElementById('rejectModal'); el.classList.remove('hidden'); el.classList.add('flex'); }
function closeReject() { var el = document.getElementById('rejectModal'); el.classList.add('hidden'); el.classList.remove('flex'); }
document.getElementById('rejectModal').addEventListener('click', closeReject);
</script>
@endif
@endsection
