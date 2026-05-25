@extends('admin.layouts.app')

@section('title', 'Related Products')
@section('breadcrumb', 'Home / Stock / Related Products')
@section('header', 'Related Products')

@section('content')
    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
            <div>
                <div class="text-sm text-slate-500">SKU</div>
                <div class="font-semibold">{{ $product->sku }} - {{ $product->name }}</div>
            </div>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Kembali</a>
        </div>

        @if ($candidates->isNotEmpty())
            <form method="post" action="{{ route('admin.products.related.sync', $product) }}" class="mb-6">
                @csrf
                <div class="flex flex-col md:flex-row md:items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Tambah Related Product</label>
                        <select name="related_ids[]" multiple required class="w-full rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-sky-500" style="min-height: 120px;">
                            @foreach ($candidates as $candidate)
                                <option value="{{ $candidate->id }}">
                                    [{{ $candidate->sku }}] {{ $candidate->name }} {{ $candidate->variant ? '('.$candidate->variant.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Tambah</button>
                </div>
                @error('related_ids')
                    <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                @enderror
            </form>
        @else
            <div class="text-sm text-slate-500 mb-6">Semua produk sudah menjadi related.</div>
        @endif

        <div class="text-sm font-semibold mb-3">Daftar Related Products</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-3 pr-4">SKU</th>
                        <th class="py-3 pr-4">Nama</th>
                        <th class="py-3 pr-4">Variant</th>
                        <th class="py-3 pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($product->relatedProducts as $related)
                    <tr class="border-b">
                        <td class="py-3 pr-4 font-medium">{{ $related->sku }}</td>
                        <td class="py-3 pr-4">{{ $related->name }}</td>
                        <td class="py-3 pr-4">{{ $related->variant ?? '-' }}</td>
                        <td class="py-3 pr-4">
                            <form method="post" action="{{ route('admin.products.related.destroy', [$product, $related]) }}" onsubmit="return confirm('Hapus related product ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-slate-500">Belum ada related product.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
