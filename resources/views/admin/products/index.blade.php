@extends('admin.layouts.app')

@section('title', 'Product')
@section('breadcrumb', 'Home / Stock / List')
@section('header', 'Manage Stock')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Tambah Stock Produk</a>

            <form method="get" class="flex items-center gap-2">
                <input name="q" value="{{ $q }}" placeholder="Search..." class="w-64 max-w-full rounded-lg border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
                @if($brand !== '')
                    <input type="hidden" name="brand" value="{{ $brand }}">
                @endif
                @if($category !== '')
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <button class="px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Cari</button>
            </form>
        </div>

        <form method="get" class="flex flex-wrap items-end gap-3 mb-4 p-3 bg-slate-50 border border-slate-200 rounded-xl">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-500 font-medium">Brand</label>
                <select name="brand" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm min-w-[160px]">
                    <option value="">Semua Brand</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->brand_code }}" @selected($brand === $b->brand_code)>{{ $b->brand_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-500 font-medium">Kategori</label>
                <select name="category" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm min-w-[160px]">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->category_code }}" @selected($category === $cat->category_code)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-slate-500 font-medium">Urutkan</label>
                <select name="sort_by" onchange="this.form.submit()" class="rounded-lg border-slate-200 text-sm min-w-[140px]">
                    <option value="name" data-dir="asc" @selected($sortBy === 'name' && $sortDir === 'asc')>Nama A → Z</option>
                    <option value="name" data-dir="desc" @selected($sortBy === 'name' && $sortDir === 'desc')>Nama Z → A</option>
                    <option value="id" data-dir="desc" @selected($sortBy === 'id' && $sortDir === 'desc')>Terbaru</option>
                    <option value="id" data-dir="asc" @selected($sortBy === 'id' && $sortDir === 'asc')>Terlama</option>
                </select>
                <input type="hidden" name="sort_dir" value="{{ $sortDir }}">
            </div>

            <input type="hidden" name="q" value="{{ $q }}">

            @if($brand !== '' || $category !== '' || $sortBy !== 'name' || $sortDir !== 'asc')
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-600 hover:bg-slate-100">Reset Filter</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-3 pr-4">Kode</th>
                        <th class="py-3 pr-4">Stock Name</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4">Brand</th>
                        <th class="py-3 pr-4">Kategori</th>
                        <th class="py-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium">{{ $product->sku }}</td>
                        <td class="py-3 pr-4">
                            <div class="font-medium">{{ $product->name }}</div>
                            @if(($product->variant ?? '') !== '')
                                <div class="text-xs text-slate-500">{{ $product->variant }}</div>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            @if($product->status_product)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-800">{{ $product->status_product }}</span>
                                <span class="text-xs text-slate-400">#{{ $product->no_urut_status }}</span>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 text-slate-600">{{ $product->brand?->brand_name ?? '-' }}</td>
                        <td class="py-3 pr-4 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.products.edit', $product) }}" class="px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-200 text-sky-700 hover:bg-sky-100">Edit</a>
                                <a href="{{ route('admin.products.related', $product) }}" class="px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100">Related</a>
                                <form method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-6 text-center text-slate-500">Tidak ada data.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-sm text-slate-500">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} entries
            </div>
            <div>{{ $products->links() }}</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sortSelect = document.querySelector('select[name="sort_by"]');
            const sortDirInput = document.querySelector('input[name="sort_dir"]');

            if (sortSelect && sortDirInput) {
                sortSelect.addEventListener('change', function () {
                    const selected = this.options[this.selectedIndex];
                    const dir = selected.getAttribute('data-dir');
                    if (dir) {
                        sortDirInput.value = dir;
                    }
                });
            }
        });
    </script>
@endsection
