<div id="product-detail-screen" class="full-screen-page" style="display: none;">
    <div class="page-header detail-header">
        <button id="close-product-detail" class="back-btn">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="header-actions">
            <div class="icon-badge" role="button" tabindex="0" data-action="openCart">
                <i class="fas fa-shopping-cart"></i><span class="badge-dot" data-cart-badge style="display: none;">0</span>
            </div>
        </div>
    </div>

    <div class="detail-content scroll-content">
        <div class="detail-image-container">
            <img data-detail-image src="https://placehold.co/400x400/009688/white?text=Product" alt="Product Detail">
        </div>

        <div class="detail-info-wrapper">
            <div class="detail-main-info">
                <h1 class="detail-title" data-detail-title>-</h1>
                <h2 class="detail-price" data-detail-price>-</h2>
                <button class="grosir-btn" type="button" data-action="comingSoon">Lihat Harga Grosir</button>
            </div>

            <div class="detail-section">
                <h3 class="detail-section-title">Detail Produk</h3>
                <div class="detail-description" data-detail-description></div>
            </div>

            <div class="detail-section row-between">
                <h3 class="detail-section-title">Brand</h3>
                <span class="font-bold" data-detail-brand></span>
            </div>

            <div class="detail-section row-between">
                <h3 class="detail-section-title">Informasi Tambahan</h3>
                <span class="detail-value" data-detail-weight></span>
            </div>
        </div>
    </div>

    <div class="detail-footer">
        <button class="add-to-cart-btn" data-detail-add-to-cart>Masukkan Keranjang</button>
    </div>
</div>
