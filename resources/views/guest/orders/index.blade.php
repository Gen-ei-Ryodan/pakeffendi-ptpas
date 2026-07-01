@extends('guest.layouts.app')

@section('title', 'Pesanan Saya - PAS Market')

@section('content')
<!-- Page Header (Desktop) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ isset($is_sales) && $is_sales ? 'Riwayat Order' : 'Pesanan' }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1 class="h3 fw-bold text-secondary mb-0">{{ isset($is_sales) && $is_sales ? 'Riwayat Order' : 'Pesanan Saya' }}</h1>
        </div>
    </div>
</section>

<!-- Orders Content (Desktop) -->
<section class="py-5 mobile-hide">
    <div class="container">
        <div class="row">
            @include('guest.partials.profile-sidebar')
            
            <div class="col-lg-9">
                @php
                    $f = $order_filters ?? [];
                    $stats = $order_stats ?? ['total_nominal' => 0, 'total_transaksi' => 0];
                @endphp

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="fw-bold mb-0">{{ isset($is_sales) && $is_sales ? 'Riwayat Order' : 'Pesanan Saya' }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('guest.orders.index') }}" class="row g-2 align-items-end mb-3" data-ajax="false">
                            @if(isset($is_sales) && $is_sales)
                                <div class="col-12 col-md-4">
                                    <label class="form-label mb-1">Nama Customer</label>
                                    <input type="text" name="customer" value="{{ $f['customer'] ?? '' }}" class="form-control form-control-sm" placeholder="Contoh: Budi">
                                </div>
                            @endif
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1">Dari</label>
                                <input type="date" name="date_from" value="{{ $f['date_from'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1">Sampai</label>
                                <input type="date" name="date_to" value="{{ $f['date_to'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Search</label>
                                <input type="text" name="q" value="{{ $f['q'] ?? '' }}" class="form-control form-control-sm" placeholder="No order / barang">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Filter Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Semua Status</option>
                                    @foreach([\App\Models\SalesOrder::STATUS_DRAFT, \App\Models\SalesOrder::STATUS_NEW, \App\Models\SalesOrder::STATUS_ON_PROGRESS, \App\Models\SalesOrder::STATUS_ON_DELIVERY, \App\Models\SalesOrder::STATUS_FINISHED] as $status)
                                        <option value="{{ $status }}" @selected(($f['status'] ?? '') === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <button class="btn btn-primary btn-sm w-100" type="submit">
                                    <i class="bi bi-funnel me-1"></i>Terapkan
                                </button>
                            </div>
                            <div class="col-6 col-md-2">
                                <a href="{{ route('guest.orders.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </a>
                            </div>
                        </form>

                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Periode</div>
                                    <div class="fw-semibold">{{ ($f['date_from'] ?? '-') }} s/d {{ ($f['date_to'] ?? '-') }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Total Nominal</div>
                                    <div class="fw-semibold">Rp {{ number_format((float) ($stats['total_nominal'] ?? 0), 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Total Transaksi</div>
                                    <div class="fw-semibold">{{ (int) ($stats['total_transaksi'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Pesanan</th>
                                        @if(isset($is_sales) && $is_sales)
                                            <th>Nama Customer</th>
                                        @endif
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($orders ?? collect()) as $order)
                                        <tr>
                                            <td class="fw-semibold">
                                                @if($order->status === \App\Models\SalesOrder::STATUS_DRAFT)
                                                    <span class="text-muted">Draft</span>
                                                @else
                                                    {{ $order->order_no }}
                                                @endif
                                            </td>
                                            @if(isset($is_sales) && $is_sales)
                                                <td>{{ $order->customer?->full_name ?? '-' }}</td>
                                            @endif
                                            <td>{{ $order->order_date?->format('Y-m-d') }}</td>
                                            <td>
                                                @if($order->status === \App\Models\SalesOrder::STATUS_DRAFT)
                                                    <span class="badge bg-warning text-dark">Draft</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                            <td>
                                                @if($order->status === \App\Models\SalesOrder::STATUS_DRAFT && isset($is_sales) && $is_sales)
                                                    <form method="POST" action="{{ route('guest.cart.load-draft', $order) }}" style="display:inline;" data-ajax="false">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <i class="bi bi-cart-plus"></i> Load
                                                        </button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('guest.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ (isset($is_sales) && $is_sales) ? 6 : 5 }}" class="text-muted text-center py-4">Tidak ada order untuk filter/periode ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if(isset($orders) && method_exists($orders, 'links'))
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE ORDERS ====================== -->
<section class="d-lg-none" id="mobOrdersSection">
    @php
        $statusList = [
            '' => 'Semua',
            \App\Models\SalesOrder::STATUS_DRAFT => 'Draft',
            \App\Models\SalesOrder::STATUS_NEW => 'Belum Bayar',
            \App\Models\SalesOrder::STATUS_ON_PROGRESS => 'Dikemas',
            \App\Models\SalesOrder::STATUS_ON_DELIVERY => 'Dikirim',
            \App\Models\SalesOrder::STATUS_FINISHED => 'Selesai',
        ];
        $activeStatus = $f['status'] ?? '';
    @endphp

    <!-- Header -->
    <div class="mob-orders-header">
        <h1 class="mob-orders-title">Pesanan Saya</h1>
        <button class="mob-orders-search-btn" id="mobOrdersSearchBtn" aria-label="Cari">
            <i class="bi bi-search"></i>
        </button>
    </div>

    <!-- Status Tabs (scrollable) -->
    <div class="mob-orders-tabs-wrap">
        <div class="mob-orders-tabs" id="mobOrdersTabs">
            @foreach($statusList as $val => $label)
            <a href="{{ $val ? url('/orders?status='.$val) : url('/orders') }}"
               class="mob-orders-tab {{ $activeStatus === $val ? 'active' : '' }}"
               data-status="{{ $val }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <!-- Search bar (collapsible) -->
    <div class="mob-orders-search-bar" id="mobOrdersSearchBar" style="display:none;">
        <form method="get" action="{{ route('guest.orders.index') }}" data-ajax="false">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <div class="mob-orders-search-inner">
                <i class="bi bi-search"></i>
                <input type="text" name="q" value="{{ $f['q'] ?? '' }}" placeholder="Cari no. pesanan...">
                <button type="submit" class="mob-orders-search-go">Cari</button>
            </div>
        </form>
    </div>

    <!-- Orders List -->
    <div class="mob-orders-list">
        @forelse(($orders ?? collect()) as $order)
        @php $isDraft = $order->status === \App\Models\SalesOrder::STATUS_DRAFT; @endphp
        <div class="mob-order-card">
            <div class="mob-order-card-top">
                <span class="mob-order-card-no">{{ $isDraft ? 'Draft' : $order->order_no }}</span>
                <span class="mob-order-card-status {{ $isDraft ? 'status-badge-draft' : 'status-badge-'.strtolower($order->status) }}">{{ $isDraft ? 'Draft' : $order->status }}</span>
            </div>
            @if(isset($is_sales) && $is_sales && $order->customer)
            <div class="mob-order-card-customer">{{ $order->customer->full_name ?? '-' }}</div>
            @endif
            <div class="mob-order-card-mid">
                <span class="mob-order-card-date">{{ $order->order_date?->format('d M Y') }}</span>
                <span class="mob-order-card-total">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
            </div>
            <div class="mob-order-card-bot">
                @if($isDraft && isset($is_sales) && $is_sales)
                <form method="POST" action="{{ route('guest.cart.load-draft', $order) }}" style="display:inline;" data-ajax="false">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm w-100">
                        <i class="bi bi-cart-plus"></i> Muat ke Keranjang
                    </button>
                </form>
                @else
                <a href="{{ route('guest.orders.show', $order) }}" class="mob-order-card-detail">
                    Lihat Detail <i class="bi bi-chevron-right"></i>
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="mob-orders-empty">
            <i class="bi bi-inbox"></i>
            <p>Tidak ada pesanan</p>
        </div>
        @endforelse
    </div>

    @if(isset($orders) && method_exists($orders, 'links') && $orders->hasPages())
    <div class="mob-orders-pagination">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchBtn = document.getElementById('mobOrdersSearchBtn');
    var searchBar = document.getElementById('mobOrdersSearchBar');
    if (searchBtn && searchBar) {
        searchBtn.addEventListener('click', function() {
            var isVisible = searchBar.style.display !== 'none';
            searchBar.style.display = isVisible ? 'none' : 'block';
        });
    }
});
</script>
@endpush
