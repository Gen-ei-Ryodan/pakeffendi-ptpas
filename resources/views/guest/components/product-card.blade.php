{{-- Product Card Component --}}
@props(['product', 'showBadge' => true])

<div class="product-card" data-product-id="{{ $product['id'] ?? 1 }}">
    <div class="position-relative">
        <img src="{{ $product['image'] ?? asset('guest/img/placeholder-product.svg') }}" 
             alt="{{ $product['name'] ?? 'Product' }}" class="product-image"
             onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
        
        @if($showBadge)
            <div class="product-badge">
                @if($product['is_new'] ?? false)
                    <span class="badge badge-new">BARU</span>
                @endif
                @if(($product['discount'] ?? 0) > 0)
                    <span class="badge badge-discount">-{{ $product['discount'] }}%</span>
                @endif
                @if($product['is_featured'] ?? false)
                    <span class="badge badge-featured">UNGGULAN</span>
                @endif
            </div>
        @endif
        
    </div>
    
    <div class="product-info">
        @if(isset($product['brand']))
            <div class="mb-2">
                <span class="text-muted small">{{ $product['brand'] }}</span>
            </div>
        @endif
        
        <h6 class="product-title text-truncate-2">{{ $product['name'] ?? 'Nama Produk' }}</h6>
        
        @if(isset($product['rating']))
            <div class="rating mb-2">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($product['rating']))
                        <i class="bi bi-star-fill"></i>
                    @elseif($i - 0.5 <= $product['rating'])
                        <i class="bi bi-star-half"></i>
                    @else
                        <i class="bi bi-star text-muted"></i>
                    @endif
                @endfor
                <small class="text-muted ms-1">({{ $product['rating'] ?? 0 }})</small>
            </div>
        @endif
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="product-price">Rp {{ number_format($product['price'] ?? 100000, 0, ',', '.') }}</span>
                @if(isset($product['original_price']) && $product['original_price'] > ($product['price'] ?? 0))
                    <small class="text-muted text-decoration-line-through d-block">
                        Rp {{ number_format($product['original_price'], 0, ',', '.') }}
                    </small>
                @endif
            </div>
            <button class="btn btn-primary btn-sm btn-add-to-cart" data-product-id="{{ $product['id'] ?? 1 }}">
                <i class="bi bi-cart-plus"></i>
            </button>
        </div>
    </div>
</div>
