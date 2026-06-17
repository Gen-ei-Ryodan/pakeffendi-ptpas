@extends('admin.layouts.app')

@section('title', 'Create Category')
@section('breadcrumb', 'Home / Product Category / Create')
@section('header', 'Create Product Category')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm w-full">
        <form method="post" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Category Image</label>
                <input type="file" name="image" accept="image/*" class="w-full">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Category Name *</label>
                <input name="name" value="{{ old('name') }}" required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Status</label>
                <div class="flex items-center gap-3 mt-1">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                        <span class="text-sm font-medium text-green-700">Active</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }} class="text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-red-700">Inactive</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Simpan</button>
                <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Batal</a>
            </div>
        </form>
    </div>
@endsection
