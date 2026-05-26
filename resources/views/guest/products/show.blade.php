@extends('guest.layouts.app')

@section('title', 'Detail Produk - PAS Market')

@section('content')
@php
    $mainImageUrl = $product->photo_url;
@endphp
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/products') }}">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Produk</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Detail -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <!-- Main Image -->
                        <div class="position-relative">
                            <img src="{{ $mainImageUrl }}" 
                                 alt="{{ $product->name }}" class="img-fluid w-100" id="mainProductImage" style="max-height: 500px; object-fit: cover;"
                                 onerror="this.style.display='none';var w=document.createElement('div');w.className='alert alert-warning text-center m-3';w.textContent='Gambar tidak tersedia';this.parentElement.appendChild(w)">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" data-product-id="{{ $product->id }}">
                    <div class="card-body">
                        <!-- Brand and Category -->
                        <div class="mb-3">
                            <span class="badge bg-secondary mb-2">{{ $product->category?->name }}</span>
                            <span class="text-muted">Brand: {{ $product->brand?->brand_name }}</span>
                        </div>
                        
                        <!-- Product Title -->
                        <h1 class="h3 fw-bold text-secondary mb-3 product-name">{{ $product->name }}</h1>
                        @if(($product->variant ?? '') !== '')
                            <div class="text-muted mb-3">{{ $product->variant }}</div>
                        @endif
                        
                        <!-- Price -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="h2 fw-bold text-primary mb-0 product-price">Rp {{ number_format((float) ($product->pricing_tiers[0]['net_price'] ?? $product->price_1), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Tiered Pricing -->
                        @if(count($product->pricing_tiers) > 1)
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Harga Grosir</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <tbody>
                                        @foreach($product->pricing_tiers as $tier)
                                        <tr class="{{ $loop->first ? 'table-primary' : '' }}">
                                            <td class="fw-medium">
                                                @if($tier['qty_end'])
                                                    {{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs
                                                @else
                                                    {{ $tier['qty_start'] }}+ pcs
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-primary">Rp {{ number_format((float) $tier['net_price'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Deskripsi Produk</h6>
                            <p class="text-muted">{{ $product->description ?: 'Deskripsi belum tersedia.' }}</p>
                        </div>
                        
                        <!-- Specifications -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Spesifikasi</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">SKU</small>
                                    <span>{{ $product->sku }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Unit</small>
                                    <span>{{ $product->unit }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Berat</small>
                                    <span>{{ number_format((float) $product->weight_kg, 2, ',', '.') }} kg</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Kategori</small>
                                    <span>{{ $product->category?->name }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quantity and Add to Cart -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Jumlah</h6>
                            <div class="d-flex align-items-center gap-3">
                                <div class="input-group quantity-control" style="width: 160px;">
                                    <button class="btn btn-secondary btn-sm btn-quantity flex-shrink-0" type="button" data-action="decrease">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="999">
                                    <button class="btn btn-secondary btn-sm btn-quantity flex-shrink-0" type="button" data-action="increase">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <button class="btn btn-primary btn-lg flex-fill btn-add-to-cart" data-product-id="{{ $product->id }}">
                                <i class="bi bi-cart-plus me-2"></i>Tambah ke Keranjang
                            </button>
                        </div>
                        
                        <!-- Additional Info -->
                        <div class="border-top pt-3">
                            <div class="row g-3 text-center">
                                <div class="col-4">
                                    <i class="bi bi-truck text-primary fs-4"></i>
                                    <div class="small text-muted">Gratis Ongkir</div>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-shield-check text-primary fs-4"></i>
                                    <div class="small text-muted">Garansi Resmi</div>
                                </div>
                                <div class="col-4">
                                    <i class="bi bi-arrow-repeat text-primary fs-4"></i>
                                    <div class="small text-muted">30 Hari Retur</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold text-secondary">Produk Terkait</h3>
                    <a href="{{ url('/products') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>

                @if(($relatedProducts ?? collect())->isNotEmpty())
                <div class="row g-3 g-lg-4">
                    @foreach($relatedProducts as $related)
                    @php
                        $relatedImageUrl = $related->photo_url;
                    @endphp
                    <div class="col-6 col-md-3">
                        <a href="{{ url('/products/'.$related->id) }}" class="text-decoration-none">
                            <div class="product-card" data-product-id="{{ $related->id }}">
                                <div class="position-relative">
                                    <img src="{{ $relatedImageUrl }}"
                                         alt="{{ $related->name }}" class="product-image"
                                         onerror="this.closest('.col-6,.col-md-3').remove()">
                                </div>

                                <div class="product-info">
                                    <div class="mb-2">
                                        <span class="text-muted small">{{ $related->brand?->brand_name }}</span>
                                    </div>
                                    <h6 class="product-title text-truncate-2">{{ $related->name }}</h6>
                                    @if(($related->variant ?? '') !== '')
                                        <div class="text-muted small text-truncate">{{ $related->variant }}</div>
                                    @endif

                                    <div class="pricing-tiers">
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
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <button class="btn btn-primary btn-sm btn-add-to-cart product-cart-btn" data-product-id="{{ $related->id }}">
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <p class="mb-0">Belum ada produk terkait.</p>
                </div>
                @endif
            </div>
        </div>
        
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    if (decreaseBtn && increaseBtn && quantityInput) {
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value);
            let maxValue = parseInt(quantityInput.max) || 10;
            if (currentValue < maxValue) {
                quantityInput.value = currentValue + 1;
            }
        });
    }
    
    // Add to cart
    if (addToCartBtn) {
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
    
    // Thumbnail image click
    document.querySelectorAll('.img-thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.img-thumbnail').forEach(t => t.classList.remove('border-primary'));
            this.classList.add('border-primary');
        });
    });
});
</script>
@endpush
