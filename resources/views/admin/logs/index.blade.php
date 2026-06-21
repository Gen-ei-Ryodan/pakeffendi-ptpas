@extends('admin.layouts.app')

@section('title', 'Logbook')
@section('breadcrumb', 'Home / Advance / Logbook')
@section('header', 'Activity Log')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-slate-700 to-slate-600 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Activity Log</h2><p class="text-xs text-white/80">Read-only log aktivitas sistem</p></div>
            </div>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari log..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">Cari</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">#</th><th class="py-3.5 px-4">Description</th><th class="py-3.5 px-4">Data</th><th class="py-3.5 px-4">Actor</th><th class="py-3.5 px-4">Waktu</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($logs as $log)
                <tr class="hover:bg-slate-50 align-top">
                    <td class="py-3.5 px-4 text-slate-500">{{ $logs->firstItem() + $loop->index }}</td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $log->description }}</td>
                    <td class="py-3.5 px-4 text-slate-500 text-xs max-w-xs truncate">{{ $log->data }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">{{ $log->actor?->name ?? '-' }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-500 text-xs">{{ $log->created_at?->format('d-m-Y H:i:s') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-500">Belum ada log.</td></tr>
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
