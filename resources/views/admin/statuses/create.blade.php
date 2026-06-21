@extends('admin.layouts.app')

@section('title', 'Create Status')
@section('breadcrumb', 'Home / Master / Status / Create')
@section('header', 'Create Product Status')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><h2 class="text-lg font-semibold text-white">Tambah Status Produk</h2><p class="text-sm text-white/80">Buat status baru untuk produk</p></div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.statuses.store') }}" class="p-6 space-y-6">
            @csrf
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11a1 1 0 11-2 0 1 1 0 012 0zm0-3a1 1 0 01-2 0V7a1 1 0 112 0v3z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Status</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Code <span class="text-red-500">*</span></label>
                        <input name="code" value="{{ old('code') }}" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2" placeholder="TERLARIS">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                        <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2" placeholder="Terlaris">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.statuses.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Simpan</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
