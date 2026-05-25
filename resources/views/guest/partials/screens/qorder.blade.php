<div id="qorder-screen" class="full-screen-page" style="display: none;">
    <div class="app-header qorder-header">
        <div class="search-bar">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Cari produk" data-qorder-search>
            <i class="fas fa-times text-muted qorder-clear-search"></i>
        </div>
        <div class="icon-badge text-accent" role="button" tabindex="0" data-action="openCart">
            <i class="fas fa-shopping-cart"></i>
            <span class="badge-dot" data-cart-badge style="display: none;"></span>
        </div>
    </div>

    <div class="scroll-content qorder-scroll-content">
        <div class="qorder-container">
            @foreach($featuredProducts as $product)
                <div class="qorder-card" data-product-id="{{ $product->id }}">
                    <div class="qorder-img">
                        @if($product->has_photo)
                            <img src="{{ $product->photo_url }}" alt="{{ $product->name }}" onerror="this.closest('.qorder-card').remove()">
                        @else
                            <i class="far fa-image qorder-placeholder-icon"></i>
                        @endif
                    </div>
                    <div class="qorder-info">
                        <h4 class="qorder-title">{{ $product->name }}</h4>
                        @if(($product->variant ?? '') !== '')
                            <div class="text-muted small mb-1">{{ $product->variant }}</div>
                        @endif
                        <div class="qorder-tiers">
                            @foreach($product->pricing_tiers as $tier)
                                <div class="tier {{ $loop->first ? 'active' : '' }}">
                                    <span>
                                        @if($tier['qty_end'])
                                            {{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs
                                        @else
                                            {{ $tier['qty_start'] }}+ pcs
                                        @endif
                                    </span>
                                    <span>Rp {{ number_format((float) $tier['price'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="qorder-actions">
                            <div class="qty-control">
                                <button class="qty-btn minus"><i class="fas fa-minus"></i></button>
                                <input type="text" value="1" class="qty-input-box qty-input" data-qorder-qty>
                                <button class="qty-btn plus"><i class="fas fa-plus"></i></button>
                            </div>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" class="qorder-checkbox qorder-checkbox-styled" data-qorder-check>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="qorder-footer">
        <button class="add-to-cart-btn" data-add-to-cart-btn>
            <i class="fas fa-shopping-cart cart-icon-margin"></i> Masukkan Keranjang
        </button>
    </div>
</div>
