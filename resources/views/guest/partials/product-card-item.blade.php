@php
    $tierCount = count($product->pricing_tiers);
@endphp
<div class="product-card" data-product-id="{{ $product->id }}">
    <div class="position-relative">
        <img src="{{ $product->photo_url }}" alt="{{ $product->name }}" class="product-image" onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
    </div>
    <div class="product-info">
        <div>
            <span class="text-muted small">{{ $product->brand?->brand_name }}</span>
        </div>
        <h6 class="product-title text-truncate-2">{{ $product->name }}</h6>
        @if(($product->variant ?? '') !== '')
            <div class="text-muted small text-truncate">{{ $product->variant }}</div>
        @endif
        <div class="pricing-tiers">
        @foreach($product->pricing_tiers as $tier)
        <div class="tier-row">
            @if($tier['qty_end'])
                <span class="text-muted">{{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs</span>
            @else
                <span class="text-muted">{{ $tier['qty_start'] }}+ pcs</span>
            @endif
            <span class="product-price">Rp {{ number_format((float) $tier['net_price'], 0, ',', '.') }}</span>
        </div>
        @endforeach
        @for($i = $tierCount; $i < 3; $i++)
        <div class="tier-row tier-row-hidden">
            <span class="text-muted">-</span>
            <span class="product-price">-</span>
        </div>
        @endfor
        </div>
        <div class="product-card-actions d-flex justify-content-end">
            <button class="btn btn-primary btn-sm btn-add-to-cart product-cart-btn" data-product-id="{{ $product->id }}">
                <i class="bi bi-cart-plus"></i>
                <span class="d-none d-lg-inline ms-1">Tambah</span>
            </button>
        </div>
    </div>
</div>
