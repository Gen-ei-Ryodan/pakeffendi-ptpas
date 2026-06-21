@extends('admin.layouts.app')

@section('title', 'Product')
@section('breadcrumb', 'Home / Stock / List')
@section('header', 'Manage Stock')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Daftar Produk</h2><p class="text-xs text-white/80">Kelola stok produk</p></div>
            </div>
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sky-700 text-sm font-semibold hover:bg-sky-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Tambah Produk
            </a>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari produk..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2">
            </div>
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            @if($brand !== '')<input type="hidden" name="brand" value="{{ $brand }}">@endif
            @if($category !== '')<input type="hidden" name="category" value="{{ $category }}">@endif
            <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">Cari</button>
        </form>
    </div>
    <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
        <form method="get" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="text-xs text-slate-500 font-medium block mb-1">Brand</label>
                <select name="brand" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm min-w-[160px] py-2 px-3">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $b)<option value="{{ $b->brand_code }}" @selected($brand === $b->brand_code)>{{ $b->brand_name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 font-medium block mb-1">Kategori</label>
                <select name="category" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm min-w-[160px] py-2 px-3">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)<option value="{{ $cat->category_code }}" @selected($category === $cat->category_code)>{{ $cat->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-slate-500 font-medium block mb-1">Urutkan</label>
                <select name="sort_by" onchange="this.form.submit()" class="rounded-lg border-slate-300 text-sm min-w-[140px] py-2 px-3">
                    <option value="name" data-dir="asc" @selected($sortBy === 'name' && $sortDir === 'asc')>Nama A → Z</option>
                    <option value="name" data-dir="desc" @selected($sortBy === 'name' && $sortDir === 'desc')>Nama Z → A</option>
                    <option value="id" data-dir="desc" @selected($sortBy === 'id' && $sortDir === 'desc')>Terbaru</option>
                    <option value="id" data-dir="asc" @selected($sortBy === 'id' && $sortDir === 'asc')>Terlama</option>
                </select>
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            </div>
            <input type="hidden" name="q" value="{{ $q }}">
            @if($brand !== '' || $category !== '' || $sortBy !== 'name' || $sortDir !== 'asc')
                <a href="{{ route('admin.products.index') }}" class="px-3 py-2 rounded-lg border border-slate-300 text-sm text-slate-600 hover:bg-slate-100">Reset</a>
            @endif
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">Produk</th><th class="py-3.5 px-4">Brand</th><th class="py-3.5 px-4">Kategori</th><th class="py-3.5 px-4">Status</th><th class="py-3.5 px-4 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4">
                        <div class="font-semibold text-slate-800">{{ $product->name }}</div>
                        <div class="text-xs text-slate-400">{{ $product->sku }} @if($product->variant) &middot; {{ $product->variant }} @endif</div>
                    </td>
                    <td class="py-3.5 px-4"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">{{ $product->brand?->brand_name ?? '-' }}</span></td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                    <td class="py-3.5 px-4">
                        @if($product->status_product)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                {{ $product->status_product }}
                            </span>
                        @else
                            <span class="text-xs text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.products.edit', $product) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <a href="{{ route('admin.products.related', $product) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600" title="Related">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            </a>
                            <form method="post" action="{{ route('admin.products.destroy', $product) }}" class="inline" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-500">Belum ada produk.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Menampilkan <span class="font-medium">{{ $products->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $products->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $products->total() }}</span></div>
        <div>{{ $products->links() }}</div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.querySelector('select[name="sort_by"]');
    const sortDirInput = document.querySelector('input[name="sort_dir"]');
    if (sortSelect && sortDirInput) {
        sortSelect.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const dir = selected.getAttribute('data-dir');
            if (dir) sortDirInput.value = dir;
        });
    }
});
</script>
@endsection
