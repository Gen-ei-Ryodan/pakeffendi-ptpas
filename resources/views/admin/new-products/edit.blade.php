@extends('admin.layouts.app')

@section('title', 'Edit Produk Terbaru')
@section('breadcrumb', 'Home / Produk Terbaru / Edit')
@section('header', 'Edit Produk Terbaru')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-white">Edit Urutan Produk Terbaru</h2>
                <p class="text-xs text-white/80">Ubah urutan produk terbaru</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.new-products.update', $newProduct) }}">
        @csrf
        @method('PUT')
        <div class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Produk
                </label>
                <div class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg">
                 <div class="font-medium text-slate-800">{{ $product->name }}</div>
                 <div class="text-xs text-slate-500 mt-1">SKU: {{ $product->sku }}</div>
                 <div class="text-xs text-slate-500 mt-1">Dibuat: {{ $product->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Urutan <span class="text-rose-500">*</span>
                </label>
                 <input type="number" name="sort_order" value="{{ old('sort_order', $newProduct?->sort_order ?? 0) }}" required min="0"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 @error('sort_order') border-rose-500 @enderror"
                       placeholder="Masukkan urutan (angka)">
                <p class="text-xs text-slate-500 mt-1">Produk akan ditampilkan berdasarkan urutan dari kecil ke besar</p>
                @error('sort_order')
                    <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-end gap-2">
            <a href="{{ route('admin.new-products.index') }}"
               class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700 transition-colors">
                Update
            </button>
        </div>
    </form>
</div>
@endsection
