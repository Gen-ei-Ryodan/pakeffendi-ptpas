@extends('admin.layouts.app')

@section('title', 'Product Status')
@section('breadcrumb', 'Home / Master / Status')
@section('header', 'Manage Product Status')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <a href="{{ route('admin.statuses.create') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Tambah Status</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-3 pr-4">#</th>
                        <th class="py-3 pr-4">Code</th>
                        <th class="py-3 pr-4">Name</th>
                        <th class="py-3 pr-4">Sort Order</th>
                        <th class="py-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($statuses as $i => $status)
                    <tr class="border-b">
                        <td class="py-3 pr-4">{{ $loop->iteration }}</td>
                        <td class="py-3 pr-4 font-mono text-sm">{{ $status->code }}</td>
                        <td class="py-3 pr-4 font-medium">{{ $status->name }}</td>
                        <td class="py-3 pr-4">{{ $status->sort_order }}</td>
                        <td class="py-3 pr-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.statuses.edit', $status) }}" class="px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-200 text-sky-700 hover:bg-sky-100">Edit</a>
                                <form method="post" action="{{ route('admin.statuses.destroy', $status) }}" onsubmit="return confirm('Hapus status ini?')">
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
    </div>
@endsection
