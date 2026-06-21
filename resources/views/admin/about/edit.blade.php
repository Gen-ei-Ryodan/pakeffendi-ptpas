@extends('admin.layouts.app')

@section('title', 'About Us')
@section('breadcrumb', 'Home / Advance / About us')
@section('header', 'About us')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><h2 class="text-lg font-semibold text-white">Edit Halaman About Us</h2><p class="text-sm text-white/80">Perbarui konten halaman tentang kami</p></div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.about.update') }}" class="p-6 space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Rich Text Editor</label>
                <textarea id="content" name="content" rows="16" class="w-full rounded-lg border border-slate-300 px-3.5 py-3 text-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20">{{ old('content', $page->content) }}</textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Simpan</div>
                </button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>tinymce.init({selector:'#content',menubar:false,height:420,plugins:'lists link image table code',toolbar:'undo redo | fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code'});</script>
@endsection
