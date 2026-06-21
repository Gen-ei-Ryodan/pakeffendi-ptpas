@extends('admin.layouts.app')

@section('title', 'Broadcast')
@section('breadcrumb', 'Home / Broadcast / List')
@section('header', 'Broadcast')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Daftar Broadcast</h2><p class="text-xs text-white/80">Kelola pengumuman broadcast</p></div>
            </div>
            <a href="{{ route('admin.broadcasts.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sky-700 text-sm font-semibold hover:bg-sky-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Tambah Broadcast
            </a>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari broadcast..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">Cari</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">#</th><th class="py-3.5 px-4">Image</th><th class="py-3.5 px-4">Deskripsi</th><th class="py-3.5 px-4 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($broadcasts as $broadcast)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4 text-slate-500">{{ $broadcasts->firstItem() + $loop->index }}</td>
                    <td class="py-3.5 px-4">
                        <img src="{{ asset('storage/'.$broadcast->image_path) }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                    </td>
                    <td class="py-3.5 px-4 text-slate-700 max-w-xs truncate">{{ $broadcast->description }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.broadcasts.edit', $broadcast) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="post" action="{{ route('admin.broadcasts.destroy', $broadcast) }}" class="inline" onsubmit="return confirm('Hapus broadcast ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-12 text-center text-slate-500">Belum ada broadcast.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Menampilkan <span class="font-medium">{{ $broadcasts->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $broadcasts->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $broadcasts->total() }}</span></div>
        <div>{{ $broadcasts->links() }}</div>
    </div>
</div>
@endsection
