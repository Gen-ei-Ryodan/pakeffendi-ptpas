@extends('admin.layouts.app')

@section('title', 'Edit Sales Order')
@section('breadcrumb', 'Home / Sales Order / Edit')
@section('header', 'Edit Sales Order')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div><h2 class="text-lg font-semibold text-white">Edit Sales Order</h2><p class="text-sm text-white/80">{{ $order->order_no }}</p></div>
            </div>
        </div>
        <form method="post" action="{{ route('admin.sales-orders.update', $order) }}" class="p-6 space-y-6">
            @csrf @method('PUT')
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center"><svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11a1 1 0 11-2 0 1 1 0 012 0zm0-3a1 1 0 01-2 0V7a1 1 0 112 0v3z" clip-rule="evenodd"/></svg></div>
                    <h3 class="text-sm font-semibold text-slate-800">Order Status</h3>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Order Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm focus:border-sky-500 focus:ring-2">
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.sales-orders.show', $order) }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-gradient-to-r from-sky-600 to-sky-500 text-white text-sm font-semibold hover:from-sky-700 hover:to-sky-600 shadow-sm shadow-sky-200">
                    <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg> Update</div>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
