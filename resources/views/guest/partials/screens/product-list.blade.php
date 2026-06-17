<div id="product-list-screen" class="full-screen-page" style="display: none;">
    <div class="page-header search-header">
        <button id="close-product-list" class="back-btn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="search-bar compact">
            <i class="fas fa-search search-icon"></i>
            <input type="text" placeholder="Cari produk" data-product-search>
        </div>
        <div class="header-actions compact">
            <div class="icon-badge" role="button" tabindex="0" data-action="openNotif"><i class="fas fa-bell"></i></div>
            <div class="icon-badge" role="button" tabindex="0" data-action="openCart">
                <i class="fas fa-shopping-cart"></i><span class="badge-dot" data-cart-badge style="display: none;"></span>
            </div>
        </div>
    </div>

    <div class="filter-sort-bar">
        <button class="filter-btn" type="button" data-action="comingSoon">
            Filter <i class="fas fa-filter"></i>
        </button>
        <div class="sort-dropdown">
            <select id="sort-select">
                <option value="terbaru">Produk Terbaru</option>
                <option value="termahal">Produk Termahal</option>
                <option value="termurah">Produk Termurah</option>
            </select>
        </div>
    </div>

    <div class="product-list-content">
        <div class="product-grid" data-product-grid>
            @foreach($featuredProducts as $product)
                <div class="product-card grid-item" data-product-id="{{ $product->id }}">
                    <div class="prod-img-box">
                        @php
                            $imgUrl = $product->photo_url;
                        @endphp
                        <img src="{{ $imgUrl }}" alt="Product" onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                    </div>
                    <div class="prod-info">
                        <p class="prod-brand">{{ $product->brand?->brand_name }}</p>
                        <p class="prod-name text-truncate-2">{{ $product->name }}</p>
                        @if(($product->variant ?? '') !== '')
                            <p class="text-muted small mb-1">{{ $product->variant }}</p>
                        @endif
                        <div class="prod-tiers">
                            @php
                                $isLoggedIn = Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales());
                            @endphp
                            @if($isLoggedIn)
                                @foreach($product->pricing_tiers as $tier)
                                    <div class="tier-row">
                                        <span class="tier-qty">
                                            @if($tier['qty_end'])
                                                {{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs
                                            @else
                                                {{ $tier['qty_start'] }}+ pcs
                                            @endif
                                        </span>
                                        <span class="tier-price">Rp {{ number_format((float) $tier['net_price'], 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="tier-row">
                                    <span class="tier-qty">1 pcs</span>
                                    <span class="tier-price">Rp {{ number_format((float) $product->price_1, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
