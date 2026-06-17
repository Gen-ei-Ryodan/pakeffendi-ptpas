<div class="scroll-content">
    <div class="banner-carousel">
        <div class="banner-slide">
            <div class="banner-content">
                <div class="banner-text">
                    <h3>Promo Terbaru</h3>
                    <p>Update dari admin langsung tampil di sini</p>
                    <button class="btn-banner" type="button" data-action="openProductList">Lihat Produk</button>
                </div>
                <img src="{{ $broadcasts->first()?->image_path ?: 'https://placehold.co/600x300/1a1a1a/white?text=PAS+Banner' }}" alt="Banner Promo" class="banner-img"
                     onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-banner.svg') }}'">
            </div>
        </div>
        <div class="carousel-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>

    <div class="section-container">
        <h3 class="section-title">Kategori Pilihan</h3>
        <div class="categories-scroll">
            @foreach($categories->take(10) as $category)
                <div class="category-card blue" role="button" tabindex="0" data-category-id="{{ $category->category_code }}">
                    <span class="cat-name">{{ $category->name }}</span>
                    <i class="fas fa-box-open cat-icon"></i>
                </div>
            @endforeach
        </div>
    </div>

    @foreach($statusProducts as $item)
    <div class="section-container">
        <div class="section-header">
            <h3 class="section-title">{{ $item['status']->name }}</h3>
            <a href="#productList" class="see-all" data-action="openProductList">Lihat semua <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="products-scroll">
            @foreach($item['products'] as $product)
                <div class="product-card" data-product-id="{{ $product->id }}">
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
                        <div class="pricing-tiers">
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
                                <p class="prod-price">Rp {{ number_format((float) $product->price_1, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @include('guest.partials.footer')
</div>
