@extends('admin.layouts.app')

@section('title', 'Favorite Brand')
@section('breadcrumb', 'Home / Favorite Brand / List')
@section('header', 'Manage Favorite Brands')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Brand Favorit</h2><p class="text-xs text-white/80">Kelola brand favorit yang ditampilkan</p></div>
            </div>
            <a href="{{ route('admin.favorite-brands.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-amber-700 text-sm font-semibold hover:bg-amber-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Tambah Brand Favorit
            </a>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">Cari</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">#</th><th class="py-3.5 px-4">Brand</th><th class="py-3.5 px-4 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($favorites as $fav)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4 text-slate-500">{{ $favorites->firstItem() + $loop->index }}</td>
                    <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $fav->brand?->brand_name }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center">
                            <form method="post" action="{{ route('admin.favorite-brands.destroy', $fav) }}" class="inline" onsubmit="return confirm('Hapus brand favorit ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-12 text-center text-slate-500">Belum ada brand favorit.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Menampilkan <span class="font-medium">{{ $favorites->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $favorites->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $favorites->total() }}</span></div>
        <div>{{ $favorites->links() }}</div>
    </div>
</div>
@endsection
