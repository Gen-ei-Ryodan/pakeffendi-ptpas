<div id="brand-screen" class="full-screen-page" style="display: none;">
    <div class="page-header">
        <button class="back-btn" type="button" onclick="history.back()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <h2 class="page-title">Brand</h2>
    </div>

    <div class="brand-content scroll-content">
        <div class="brand-grid">
            @foreach($brands as $brand)
                <div class="brand-card" role="button" tabindex="0" data-brand-id="{{ $brand->brand_code }}">
                    <img src="{{ $brand->brand_image_path ?: 'https://placehold.co/100x50/white/red?text='.urlencode($brand->brand_name) }}"
                         alt="{{ $brand->brand_name }}"
                         class="brand-logo-img"
                         onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                    <span class="brand-name">{{ $brand->brand_name }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
