@extends('admin.layouts.app')

@section('title', 'Tambah Produk Terlaris')
@section('breadcrumb', 'Home / Produk Terlaris / Create')
@section('header', 'Tambah Produk Terlaris')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-white">Tambah Produk Terlaris</h2>
                <p class="text-xs text-white/80">Tambahkan produk ke daftar produk terlaris</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.featured-products.store') }}">
        @csrf
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Produk <span class="text-rose-500">*</span>
                </label>
                <select name="product_id" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 @error('product_id') border-rose-500 @enderror">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Urutan <span class="text-rose-500">*</span>
                </label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required min="0"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 @error('sort_order') border-rose-500 @enderror"
                       placeholder="Masukkan urutan (angka)">
                <p class="text-xs text-slate-500 mt-1">Produk akan ditampilkan berdasarkan urutan dari kecil ke besar</p>
                @error('sort_order')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
            <a href="{{ route('admin.featured-products.index') }}"
               class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700 transition-colors">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
