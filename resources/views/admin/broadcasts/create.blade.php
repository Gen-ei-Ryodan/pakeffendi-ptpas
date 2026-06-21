@extends('admin.layouts.app')

@section('title', 'Create Broadcast')
@section('breadcrumb', 'Home / Broadcast / Create')
@section('header', 'Create Broadcast')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div><h2 class="text-lg font-semibold text-white">Tambah Broadcast Baru</h2><p class="text-sm text-white/80">Buat pengumuman broadcast</p></div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.broadcasts.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Informasi Broadcast</h3>
                </div>
                <div class="grid grid-cols-1 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Broadcast Image <span class="text-red-500">*</span></label>
                        <input type="file" name="image" accept="image/*" required class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2" placeholder="Deskripsi broadcast">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.broadcasts.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Simpan</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
