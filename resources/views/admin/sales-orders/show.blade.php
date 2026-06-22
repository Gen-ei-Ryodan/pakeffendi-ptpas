@extends('admin.layouts.app')

@section('title', 'Sales Order Detail')
@section('breadcrumb', 'Home / Sales Order / Detail')
@section('header', 'Detail Sales Order')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm text-slate-500">{{ $order->order_no }}</div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales-orders.edit', $order) }}" class="px-4 py-2 rounded-lg bg-sky-600 text-white font-semibold hover:bg-sky-700">Edit</a>
            <a href="{{ route('admin.sales-orders.index') }}" class="px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-100">Kembali</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="text-sm font-semibold mb-3">Informasi Header</div>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-slate-500">Sales Order No</dt>
                    <dd class="font-medium">{{ $order->order_no }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Sales Order Date</dt>
                    <dd class="font-medium">{{ optional($order->order_date)->format('d-m-Y H:i:s') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Customer</dt>
                    <dd class="font-medium">{{ $order->customer?->customer_code }} - {{ $order->customer?->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Payment Type</dt>
                    <dd class="font-medium">{{ $order->payment_type }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Sales Person</dt>
                    <dd class="font-medium">{{ $order->salesPerson?->name }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Status</dt>
                    <dd class="font-medium">{{ $order->status }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Shipping Fee</dt>
                    <dd class="font-medium">{{ number_format((float) $order->shipping_fee, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Sales Grand Total</dt>
                    <dd class="font-medium">{{ number_format((float) $order->grand_total, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">DPP</dt>
                    <dd class="font-medium">{{ number_format((float) $order->dpp, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">PPN</dt>
                    <dd class="font-medium">{{ number_format((float) $order->ppn, 2) }} ({{ number_format((float) $order->ppn_percent, 2) }}%)</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Process Date</dt>
                    <dd class="font-medium">{{ optional($order->process_date)->format('d-m-Y') }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Process Time</dt>
                    <dd class="font-medium">{{ $order->process_time }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Process Order No</dt>
                    <dd class="font-medium">{{ $order->process_order_no }}</dd>
                </div>
                <div class="md:col-span-2">
                    <dt class="text-slate-500">Notes</dt>
                    <dd class="font-medium">{{ $order->notes }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="text-sm font-semibold mb-3">Informasi Pengiriman</div>
            <dl class="grid grid-cols-1 gap-3 text-sm">
                <div>
                    <dt class="text-slate-500">Delivery To</dt>
                    <dd class="font-medium">{{ $order->delivery_to }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Delivery Phone</dt>
                    <dd class="font-medium">{{ $order->delivery_phone }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Delivery Address</dt>
                    <dd class="font-medium">{{ $order->delivery_address }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        <div class="text-sm font-semibold mb-3">Sales Order Detail</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 border-b">
                        <th class="py-3 pr-4">#</th>
                        <th class="py-3 pr-4">Nama Produk</th>
                        <th class="py-3 pr-4">Jumlah</th>
                        <th class="py-3 pr-4">Netprice</th>
                        <th class="py-3 pr-4">Diskon</th>
                        <th class="py-3 pr-4">Final</th>
                        <th class="py-3 pr-4 text-center">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sum = 0; @endphp
                    @foreach ($order->items as $i => $item)
                        @php
                            $sum += (float) $item->final_total;
                            $sku = $item->product?->sku;
                            $stockQty = $sku && isset($stockMap[$sku]) ? $stockMap[$sku] : null;
                        @endphp
                        <tr class="border-b">
                            <td class="py-3 pr-4">{{ $i + 1 }}</td>
                            <td class="py-3 pr-4">{{ $item->product_name }}</td>
                            <td class="py-3 pr-4">{{ $item->quantity }} x {{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="py-3 pr-4">{{ number_format((float) $item->net_price, 2) }}</td>
                            <td class="py-3 pr-4">{{ number_format((float) $item->discount_percent, 2) }}%</td>
                            <td class="py-3 pr-4">{{ number_format((float) $item->final_total, 2) }}</td>
                            <td class="py-3 pr-4 text-center font-semibold">
                                @if($stockQty !== null)
                                    <span class="text-green-600">{{ number_format($stockQty, 0) }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="py-3 pr-4 text-right font-semibold">Total</td>
                        <td class="py-3 pr-4 font-semibold">{{ number_format($sum, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

