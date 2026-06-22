@extends('guest.layouts.app')

@section('title', 'Detail Pesanan - PAS Market')

@section('content')
<!-- Page Header (Desktop) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.orders.index') }}">Pesanan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Pesanan</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1 class="h3 fw-bold text-secondary mb-0">Detail Pesanan</h1>
        </div>
    </div>
</section>

<!-- Order Detail Content (Desktop) -->
<section class="py-5 mobile-hide">
    <div class="container">
        <div class="row">
            @include('guest.partials.profile-sidebar')

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0">Detail Pesanan</h5>
                                <span class="badge bg-secondary">{{ $order->status }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <h6 class="fw-semibold mb-1">No. Pesanan</h6>
                                        <p class="mb-0">{{ $order->order_no }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="fw-semibold mb-1">Tanggal Pesanan</h6>
                                        <p class="mb-0">{{ $order->order_date?->format('Y-m-d H:i') }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h6 class="fw-semibold mb-1">Penerima</h6>
                                        <p class="mb-0">{{ $order->delivery_to ?? '-' }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <h6 class="fw-semibold mb-1">Telepon</h6>
                                        <p class="mb-0">{{ $order->delivery_phone ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h6 class="fw-semibold mb-1">Alamat</h6>
                                    <p class="mb-0">{{ $order->delivery_address ?? '-' }}</p>
                                </div>
                                @if(!empty($order->notes))
                                    <div class="mt-3">
                                        <h6 class="fw-semibold mb-1">Catatan</h6>
                                        <p class="mb-0">{{ $order->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="fw-bold mb-0">Produk yang Dibeli</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th class="text-center">Jumlah</th>
                                                @if($is_sales)<th class="text-center">Stock</th>@endif
                                                <th class="text-end">Harga</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(($order->items ?? collect()) as $item)
                                                @php
                                                    $sku = $item->product?->sku;
                                                    $stockQty = $sku && isset($stockMap[$sku]) ? $stockMap[$sku] : null;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <h6 class="fw-semibold mb-1">{{ $item->product_name }}</h6>
                                                                <small class="text-muted">{{ $item->product?->brand?->brand_name ?? '' }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                                    @if($is_sales)<td class="text-center fw-semibold">
                                                        @if($stockQty !== null)
                                                            <span class="text-success">{{ number_format($stockQty, 0) }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>@endif
                                                    <td class="text-end">
                                                        Rp {{ number_format((float) $item->net_price, 0, ',', '.') }}
                                                        @if(((float) $item->discount_percent) > 0)
                                                            <div class="text-muted small">disc {{ rtrim(rtrim(number_format((float) $item->discount_percent, 2, '.', ''), '0'), '.') }}%</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-end fw-semibold">Rp {{ number_format((float) $item->final_total, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-transparent">
                                <h5 class="fw-bold mb-0">Ringkasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total</span>
                                    <span class="fw-bold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Ongkir</span>
                                    <span>Rp {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Grand Total</span>
                                    <span class="fw-bold h5 text-primary">Rp {{ number_format((float) ($order->grand_total + $order->shipping_fee), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE ORDER DETAIL ====================== -->
<section class="d-lg-none" id="mobOrderDetailSection">
    @php
        $statusLabels = [
            'new' => ['Belum Bayar', 'orange'],
            'waiting_payment' => ['Belum Bayar', 'orange'],
            'on_progress' => ['Diproses', 'blue'],
            'processing' => ['Diproses', 'blue'],
            'on_delivery' => ['Dikirim', 'green'],
            'shipping' => ['Dikirim', 'green'],
            'finished' => ['Selesai', 'purple'],
            'completed' => ['Selesai', 'purple'],
            'cancelled' => ['Dibatalkan', 'red'],
            'return' => ['Pengembalian', 'red'],
        ];
        $statusInfo = $statusLabels[strtolower($order->status)] ?? [$order->status, 'grey'];
    @endphp

    <!-- Header with back -->
    <div class="mob-order-detail-header">
        <a href="{{ url('/orders') }}" class="mob-order-detail-back"><i class="bi bi-chevron-left"></i></a>
        <h1 class="mob-order-detail-title">Detail Pesanan</h1>
    </div>

    <!-- Status banner -->
    <div class="mob-order-status-banner" style="background: var(--{{ $statusInfo[1] }}-100, #f5f5f5);">
        <div class="mob-order-status-icon-lg" style="color: var(--{{ $statusInfo[1] }}-color, #999);">
            @if(in_array(strtolower($order->status), ['finished', 'completed']))
                <i class="bi bi-check-circle-fill"></i>
            @elseif(in_array(strtolower($order->status), ['on_delivery', 'shipping']))
                <i class="bi bi-truck"></i>
            @elseif(in_array(strtolower($order->status), ['on_progress', 'processing']))
                <i class="bi bi-box-seam"></i>
            @else
                <i class="bi bi-clock"></i>
            @endif
        </div>
        <div>
            <div class="mob-order-status-label-lg" style="color: var(--{{ $statusInfo[1] }}-color, var(--secondary-color));">
                {{ $statusInfo[0] }}
            </div>
            <div class="mob-order-status-no">{{ $order->order_no }}</div>
        </div>
    </div>

    <!-- Order info card -->
    <div class="mob-order-info-card">
        <div class="mob-order-info-row">
            <span class="mob-order-info-label">Tanggal</span>
            <span class="mob-order-info-value">{{ $order->order_date?->format('d M Y, H:i') }}</span>
        </div>
        <div class="mob-order-info-row">
            <span class="mob-order-info-label">Penerima</span>
            <span class="mob-order-info-value">{{ $order->delivery_to ?? '-' }}</span>
        </div>
        <div class="mob-order-info-row">
            <span class="mob-order-info-label">Telepon</span>
            <span class="mob-order-info-value">{{ $order->delivery_phone ?? '-' }}</span>
        </div>
        <div class="mob-order-info-row mob-order-info-address">
            <span class="mob-order-info-label">Alamat</span>
            <span class="mob-order-info-value">{{ $order->delivery_address ?? '-' }}</span>
        </div>
        @if(!empty($order->notes))
        <div class="mob-order-info-row">
            <span class="mob-order-info-label">Catatan</span>
            <span class="mob-order-info-value">{{ $order->notes }}</span>
        </div>
        @endif
    </div>

    <!-- Products section -->
    <div class="mob-order-products">
        <div class="mob-order-section-title">Produk yang Dibeli</div>

        @foreach(($order->items ?? collect()) as $item)
        @php
            $sku = $item->product?->sku;
            $stockQty = $sku && isset($stockMap[$sku]) ? $stockMap[$sku] : null;
        @endphp
        <div class="mob-order-product-item">
            <div class="mob-order-product-top">
                <span class="mob-order-product-name">{{ $item->product_name }}</span>
                <span class="mob-order-product-qty">x{{ (int) $item->quantity }}</span>
            </div>
            @if($item->product?->brand?->brand_name)
            <div class="mob-order-product-brand">{{ $item->product->brand->brand_name }}</div>
            @endif
            <div class="mob-order-product-prices">
                <span class="mob-order-product-unit">Rp {{ number_format((float) $item->net_price, 0, ',', '.') }}/pcs</span>
                <span class="mob-order-product-subtotal">Rp {{ number_format((float) $item->final_total, 0, ',', '.') }}</span>
            </div>
            @if($is_sales && $stockQty !== null)
            <div class="mt-1">
                <span class="badge bg-success bg-opacity-10 text-success rounded-pill small">
                    <i class="bi bi-box-seam me-1"></i>Stock: {{ number_format($stockQty, 0) }}
                </span>
            </div>
            @endif
            @if(((float) $item->discount_percent) > 0)
            <div class="mob-order-product-disc">Disc {{ rtrim(rtrim(number_format((float) $item->discount_percent, 2, '.', ''), '0'), '.') }}%</div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Summary -->
    <div class="mob-order-summary">
        <div class="mob-order-summary-row">
            <span>Total</span>
            <span>Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
        </div>
        <div class="mob-order-summary-row">
            <span>Ongkir</span>
            <span>Rp {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</span>
        </div>
        <div class="mob-order-summary-divider"></div>
        <div class="mob-order-summary-total">
            <span>Grand Total</span>
            <span>Rp {{ number_format((float) ($order->grand_total + $order->shipping_fee), 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Back button -->
    <a href="{{ url('/orders') }}" class="mob-order-back-btn">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesanan
    </a>
</section>
@endsection
