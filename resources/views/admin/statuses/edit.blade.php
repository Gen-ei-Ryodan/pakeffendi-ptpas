@extends('admin.layouts.app')

@section('title', 'Edit Status')
@section('breadcrumb', 'Home / Master / Status / Edit')
@section('header', 'Edit Product Status')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm w-full">
        <form method="post" action="{{ route('admin.statuses.update', $status) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Code *</label>
                <input name="code" value="{{ old('code', $status->code) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500" placeholder="TERLARIS">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Name *</label>
                <input name="name" value="{{ old('name', $status->name) }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500" placeholder="Terlaris">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $status->sort_order) }}" min="0" class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Simpan</button>
                <a href="{{ route('admin.statuses.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Batal</a>
            </div>
        </form>
    </div>
@endsection
