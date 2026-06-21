@extends('admin.layouts.app')

@section('title', 'Create Favorite Brand')
@section('breadcrumb', 'Home / Favorite Brand / Create')
@section('header', 'Tambah Brand Terfavorit')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                </div>
                <div><h2 class="text-lg font-semibold text-white">Tambah Brand Favorit</h2><p class="text-sm text-white/80">Pilih brand yang ingin ditampilkan sebagai favorit</p></div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.favorite-brands.store') }}" class="p-6 space-y-6">
            @csrf
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-rose-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Pilih Brand</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Brand <span class="text-red-500">*</span></label>
                    <select name="product_brand_ids[]" multiple required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 h-48">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->brand_code }}">{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">Bisa pilih lebih dari satu (Ctrl/Cmd + klik).</p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.favorite-brands.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Simpan</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
