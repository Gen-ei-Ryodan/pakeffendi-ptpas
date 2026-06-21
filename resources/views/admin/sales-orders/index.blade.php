@extends('admin.layouts.app')

@section('title', 'Sales Order')
@section('breadcrumb', 'Home / Sales Order / List')
@section('header', 'Manage Sales Order')

@section('content')
<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-sky-600 to-sky-500 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div><h2 class="text-base font-semibold text-white">Daftar Sales Order</h2><p class="text-xs text-white/80">Kelola semua pesanan penjualan</p></div>
            </div>
        </div>
    </div>
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="get" class="flex items-center justify-end gap-2">
            <div class="relative">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input name="q" value="{{ $q }}" placeholder="Cari order..." class="w-56 pl-9 pr-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-sky-500 focus:ring-2">
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 text-white text-sm font-medium hover:bg-sky-700">Cari</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="bg-slate-50 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <th class="py-3.5 px-4">Order</th><th class="py-3.5 px-4">Customer</th><th class="py-3.5 px-4">Pembayaran</th><th class="py-3.5 px-4">Status</th><th class="py-3.5 px-4">Total</th><th class="py-3.5 px-4 text-center">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
            @forelse ($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $order->order_no }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $order->customer?->customer_code }} - {{ $order->customer?->full_name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">{{ $order->payment_type }}</span>
                    </td>
                    <td class="py-3.5 px-4">
                        @php
                            $statusColor = match($order->status) {
                                'new' => 'bg-blue-100 text-blue-700',
                                'processing' => 'bg-amber-100 text-amber-700',
                                'completed' => 'bg-emerald-100 text-emerald-700',
                                'cancelled' => 'bg-rose-100 text-rose-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ str_replace(['bg-', 'text-'], ['bg-', ''], $statusColor) }}"></span>
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 font-semibold text-slate-700">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                    <td class="py-3.5 px-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.sales-orders.show', $order) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.sales-orders.edit', $order) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-sky-50 hover:text-sky-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="post" action="{{ route('admin.sales-orders.destroy', $order) }}" class="inline" onsubmit="return confirm('Hapus sales order {{ $order->order_no }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-12 text-center text-slate-500">Belum ada sales order.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-3">
        <div class="text-sm text-slate-500">Menampilkan <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span> - <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-medium">{{ $orders->total() }}</span></div>
        <div>{{ $orders->links() }}</div>
    </div>
</div>
@endsection
