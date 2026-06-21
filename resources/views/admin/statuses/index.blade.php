@extends('admin.layouts.app')

@section('title', 'Product Status')
@section('breadcrumb', 'Home / Master / Status')
@section('header', 'Manage Product Status')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Daftar Status</h2><p class="text-xs text-white/80">Kelola status produk</p></div>
            </div>
            <a href="{{ route('admin.statuses.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sky-700 text-sm font-semibold hover:bg-sky-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg> Tambah Status
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4">#</th>
                    <th class="py-3.5 px-4">Code</th>
                    <th class="py-3.5 px-4">Name</th>
                    <th class="py-3.5 px-4">Sort Order</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($statuses as $status)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4">{{ $loop->iteration }}</td>
                    <td class="py-3.5 px-4 font-mono text-xs font-semibold text-slate-700">{{ $status->code }}</td>
                    <td class="py-3.5 px-4 font-medium text-slate-800">{{ $status->name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">{{ $status->sort_order }}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.statuses.edit', $status) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="post" action="{{ route('admin.statuses.destroy', $status) }}" class="inline" onsubmit="return confirm('Hapus status {{ $status->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="py-12 text-center text-slate-500">Belum ada status.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
