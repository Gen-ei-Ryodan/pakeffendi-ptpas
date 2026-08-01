@extends('admin.layouts.app')

@section('title', 'Sales Log')
@section('breadcrumb', 'Home / Log / Sales Log')
@section('header', 'Sales Activity Log')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Sales Activity Log</h2><p class="text-xs text-white/80">Log aktivitas sales (registrasi customer & perubahan alamat)</p></div>
            </div>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari log..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-emerald-500 focus:ring-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700">Cari</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">#</th><th class="py-3.5 px-4">Deskripsi</th><th class="py-3.5 px-4">Detail</th><th class="py-3.5 px-4">Sales</th><th class="py-3.5 px-4">Waktu</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($logs as $log)
                <tr class="hover:bg-slate-50 align-top">
                    <td class="py-3.5 px-4 text-slate-500">{{ $logs->firstItem() + $loop->index }}</td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $log->description }}</td>
                    <td class="py-3.5 px-4 text-slate-500 text-xs max-w-xs truncate">{{ $log->data }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ $log->actor?->name ?? '-' }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-500">Belum ada log sales.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Menampilkan <span class="font-medium">{{ $logs->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $logs->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $logs->total() }}</span></div>
        <div>{{ $logs->links() }}</div>
    </div>
</div>
@endsection
