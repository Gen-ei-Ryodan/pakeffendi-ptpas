@extends('admin.layouts.app')

@section('title', 'Product Category')
@section('breadcrumb', 'Home / Product Category / List')
@section('header', 'Manage Product Categories')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Tambah Category</a>

            <form method="get" class="flex items-center gap-2">
                <input name="q" value="{{ $q }}" placeholder="Search..." class="w-64 max-w-full rounded-lg border-slate-200 focus:border-sky-500 focus:ring-sky-500">
                <button class="px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-3 pr-4">#</th>
                        <th class="py-3 pr-4">Image</th>
                        <th class="py-3 pr-4">Category Name</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($categories as $i => $category)
                    <tr class="border-b">
                        <td class="py-3 pr-4">{{ $categories->firstItem() + $i }}</td>
                        <td class="py-3 pr-4">
                            @if ($category->image_path)
                                <img src="{{ asset('storage/'.$category->image_path) }}" class="w-12 h-12 rounded-lg object-cover" alt="">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-slate-100 border border-slate-200"></div>
                            @endif
                        </td>
                        <td class="py-3 pr-4 font-medium">{{ $category->name }}</td>
                        <td class="py-3 pr-4">
                            @if ($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-200 text-sky-700 hover:bg-sky-100">Edit</a>
                                <form method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Hapus category ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-500">Tidak ada data.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div class="text-sm text-slate-500">
                Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} entries
            </div>
            <div>{{ $categories->links() }}</div>
        </div>
    </div>
@endsection

