@extends('admin.layouts.app')

@section('title', 'Edit Category')
@section('breadcrumb', 'Home / Product Category / Edit')
@section('header', 'Edit Product Category')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Edit Kategori</h2>
                    <p class="text-sm text-white/80">Perbarui informasi kategori produk</p>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf @method('PUT')
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Kategori</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Category Image</label>
                        <input type="file" name="image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Category Name <span class="text-red-500">*</span></label>
                        <input name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Status</label>
                        <div class="flex items-center gap-4 mt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                <span class="text-sm font-medium text-emerald-700">Active</span>
                            </label>
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="is_active" value="0" {{ old('is_active', $category->is_active ? '1' : '0') == '0' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                <span class="text-sm font-medium text-rose-700">Inactive</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Update</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
