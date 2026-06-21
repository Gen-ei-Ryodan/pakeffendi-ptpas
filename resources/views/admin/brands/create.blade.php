@extends('admin.layouts.app')

@section('title', 'Create Brand')
@section('breadcrumb', 'Home / Brand / Create')
@section('header', 'Create Product Brand')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Tambah Brand Baru</h2>
                    <p class="text-sm text-white/80">Buat brand produk baru</p>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.brands.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11a1 1 0 11-2 0 1 1 0 012 0zm0-3a1 1 0 01-2 0V7a1 1 0 112 0v3z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Brand</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Brand Image</label>
                        <input type="file" name="brand_image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Brand Name <span class="text-red-500">*</span></label>
                        <input name="brand_name" value="{{ old('brand_name') }}" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20" placeholder="Nama brand">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.brands.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Simpan</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
