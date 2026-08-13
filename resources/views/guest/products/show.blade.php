@extends('guest.layouts.app')

@section('title', 'Detail Produk - PAS Market')

@section('content')
@php
    $mainImageUrl = $product->photo_url;
    $isLoggedIn = Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales());
@endphp

<section class="pd-page py-4 py-lg-5">
    <div class="container">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb pd-breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row g-4 g-lg-5">
            <!-- Product Image -->
            <div class="col-lg-6">
                <div class="pd-image-card">
                    <div class="pd-image-badge"><i class="bi bi-shop"></i> PAS Official</div>
                    <img src="{{ $mainImageUrl }}" alt="{{ $product->name }}" class="pd-image" id="mainProductImage"
                         onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="pd-info" data-product-id="{{ $product->id }}">
                    <div class="pd-meta">
                        <span class="pd-chip-cat"><i class="bi bi-grid"></i> {{ $product->category?->name }}</span>
                        <span class="pd-brand"><i class="bi bi-tag"></i> {{ $product->brand?->brand_name }}</span>
                    </div>

                    <h1 class="pd-title">{{ $product->name }}</h1>
                    @if(($product->variant ?? '') !== '')
                        <div class="pd-variant">{{ $product->variant }}</div>
                    @endif

                    <div class="pd-price-block">
                        <span class="pd-price-label">Harga</span>
                        <span class="pd-price product-price">
                            @if($isLoggedIn)
                                Rp {{ number_format((float) ($product->pricing_tiers[0]['net_price'] ?? $product->price_1), 0, ',', '.') }}
                            @else
                                Rp {{ number_format((float) $product->price_1, 0, ',', '.') }}
                            @endif
                        </span>
                        <span class="pd-price-note">Harga sudah termasuk PPN</span>
                    </div>

                    @if($isLoggedIn && count($product->pricing_tiers) > 1)
                    <div class="pd-tiers">
                        <h6 class="pd-block-title">Harga Grosir Bertingkat</h6>
                        <div class="pd-tier-list">
                            @foreach($product->pricing_tiers as $tier)
                            <div class="pd-tier-row {{ $loop->first ? 'is-active' : '' }}">
                                <span>
                                    @if($tier['qty_end'])
                                        {{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs
                                    @else
                                        {{ $tier['qty_start'] }}+ pcs
                                    @endif
                                </span>
                                <span class="pd-tier-price">Rp {{ number_format((float) $tier['net_price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="pd-desc">
                        <h6 class="pd-block-title">Deskripsi Produk</h6>
                        <p>{{ $product->description ?: 'Deskripsi belum tersedia.' }}</p>
                    </div>

                    <div class="pd-spec">
                        <div class="pd-spec-item"><span>SKU</span><strong>{{ $product->sku }}</strong></div>
                        <div class="pd-spec-item"><span>Unit</span><strong>{{ $product->unit }}</strong></div>
                        <div class="pd-spec-item"><span>Berat</span><strong>{{ number_format((float) $product->weight_kg, 2, ',', '.') }} kg</strong></div>
                        <div class="pd-spec-item"><span>Kategori</span><strong>{{ $product->category?->name }}</strong></div>
                    </div>

                    <div class="pd-buy">
                        <div>
                            <label class="pd-block-title" for="quantity">Jumlah</label>
                            <div class="pd-stepper">
                                <button class="pd-stepper-btn" type="button" data-action="decrease"><i class="bi bi-dash"></i></button>
                                <input type="number" class="pd-stepper-input" id="quantity" value="1" min="1" max="999999">
                                <button class="pd-stepper-btn" type="button" data-action="increase"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <button class="pd-add-btn btn-add-to-cart" data-product-id="{{ $product->id }}">
                            <i class="bi bi-cart-plus"></i>
                            <span>Tambah ke Keranjang</span>
                        </button>
                    </div>

                    <div class="pd-benefits">
                        <div class="pd-benefit"><i class="bi bi-truck"></i><span>Gratis Ongkir</span></div>
                        <div class="pd-benefit"><i class="bi bi-shield-check"></i><span>Garansi Resmi</span></div>
                        <div class="pd-benefit"><i class="bi bi-arrow-repeat"></i><span>30 Hari Retur</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="pd-related mt-5 pt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="pd-related-title">Produk Terkait</h3>
                <a href="{{ url('/products') }}" class="pd-related-link">Lihat Semua <i class="bi bi-arrow-right"></i></a>
            </div>

            @if(($relatedProducts ?? collect())->isNotEmpty())
            <div class="products-grid-manual">
                @foreach($relatedProducts as $related)
                @php
                    $relatedImageUrl = $related->photo_url;
                    $relatedTierCount = count($related->pricing_tiers);
                @endphp
                <a href="{{ url('/products/'.$related->id) }}" class="text-decoration-none d-block">
                    <div class="product-card" data-product-id="{{ $related->id }}">
                        <div class="position-relative">
                            <img src="{{ $relatedImageUrl }}" alt="{{ $related->name }}" class="product-image"
                                 onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                        </div>
                        <div class="product-info">
                            <div><span class="text-muted small">{{ $related->brand?->brand_name }}</span></div>
                            <h6 class="product-title text-truncate-2">{{ $related->name }}</h6>
                            @if(($related->variant ?? '') !== '')
                                <div class="text-muted small text-truncate">{{ $related->variant }}</div>
                            @endif
                            <div class="pricing-tiers">
                            @if($isLoggedIn)
                            @foreach($related->pricing_tiers as $tier)
                            <div class="tier-row">
                                @if($tier['qty_end'])
                                    <span class="text-muted">{{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs</span>
                                @else
                                    <span class="text-muted">{{ $tier['qty_start'] }}+ pcs</span>
                                @endif
                                <span class="product-price">Rp {{ number_format((float) $tier['net_price'], 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            @for($i = $relatedTierCount; $i < 3; $i++)
                            <div class="tier-row tier-row-hidden">
                                <span class="text-muted">-</span>
                                <span class="product-price">-</span>
                            </div>
                            @endfor
                            @else
                            <div class="tier-row">
                                <span class="text-muted">1 pcs</span>
                                <span class="product-price">Rp {{ number_format((float) $related->price_1, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary btn-sm btn-add-to-cart product-cart-btn" data-product-id="{{ $related->id }}">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="text-center text-muted py-4">
                <p class="mb-0">Belum ada produk terkait.</p>
            </div>
            @endif
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const addToCartBtn = document.querySelector('.pd-add-btn');

    if (quantityInput) {
        document.querySelectorAll('.pd-stepper-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let currentValue = parseInt(quantityInput.value) || 1;
                let maxValue = parseInt(quantityInput.max) || 999999;
                if (this.dataset.action === 'decrease' && currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
                if (this.dataset.action === 'increase' && currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                }
            });
        });
    }

    if (addToCartBtn && quantityInput) {
        addToCartBtn.addEventListener('click', function() {
            const quantity = parseInt(quantityInput.value) || 1;
            const productData = {
                id: '{{ $product->id }}',
                name: '{{ $product->name }}',
                price: {{ $product->price_1 }},
                image: document.getElementById('mainProductImage').src,
                quantity: quantity
            };

            PAS.Cart.addItem(productData);
        });
    }
});
</script>
@endpush