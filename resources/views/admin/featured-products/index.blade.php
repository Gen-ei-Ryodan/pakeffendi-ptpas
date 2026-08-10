@extends('admin.layouts.app')

@section('title', 'Produk Terlaris')
@section('breadcrumb', 'Home / Produk Terlaris / List')
@section('header', 'Produk Terlaris')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-white">Produk Terlaris</h2>
                    <p class="text-xs text-white/80">Kelola produk terlaris yang ditampilkan</p>
                </div>
            </div>
            <a href="{{ route('admin.featured-products.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white text-sky-700 text-sm font-semibold hover:bg-sky-50 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4">Urutan</th>
                    <th class="py-3.5 px-4">Gambar</th>
                    <th class="py-3.5 px-4">Produk</th>
                    <th class="py-3.5 px-4">SKU</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($featuredProducts as $featured)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4">
                        @php $inList = (int) $featured->no_urut_status > 0; @endphp
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full {{ $inList ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-500' }} font-bold text-sm">
                            {{ $featured->no_urut_status ?? 0 }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        @if($featured->photo_path)
                            <img src="{{ asset('storage/'.$featured->photo_path) }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200">
                        @else
                            <div class="w-14 h-14 rounded-lg bg-slate-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="py-3.5 px-4">
                        <div class="font-semibold text-slate-800">{{ $featured->name }}</div>
                    </td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $featured->sku }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.featured-products.edit', $featured) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="post" action="{{ route('admin.featured-products.destroy', $featured) }}" class="inline" onsubmit="return confirm('Hapus produk ini dari daftar produk terlaris?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="text-sm font-medium text-slate-500">Belum ada produk terlaris</div>
                            <a href="{{ route('admin.featured-products.create') }}" class="text-sm text-sky-600 hover:text-sky-700 font-medium">Tambah produk terlaris</a>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500">
            Menampilkan <span class="font-medium">{{ $featuredProducts->firstItem() ?? 0 }}</span> - 
            <span class="font-medium">{{ $featuredProducts->lastItem() ?? 0 }}</span> dari 
            <span class="font-medium">{{ $featuredProducts->total() }}</span>
        </div>
        <div>{{ $featuredProducts->links() }}</div>
    </div>
</div>

<div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex gap-3">
        <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-800">
            <p class="font-medium mb-1">Informasi:</p>
            <p class="mt-1">Atur urutan mulai dari angka <strong>1</strong>. Produk dengan urutan <strong>0</strong> tidak akan ditampilkan di halaman Home.</p>
        </div>
    </div>
</div>
@endsection
