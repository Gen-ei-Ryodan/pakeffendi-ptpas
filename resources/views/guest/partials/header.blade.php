<header class="app-header">
    <div class="header-content">
        <div class="nav-left">
            <div class="header-icon mobile-only" id="menu-btn"><i class="fas fa-th-large"></i></div>
            <a href="#home" class="brand desktop-only">
                <i class="fas fa-cube"></i>
                <span>PAS Market</span>
            </a>
            <button class="nav-categories desktop-only" id="search-category-btn" type="button">
                <i class="fas fa-list"></i>
                <span>Kategori</span>
                <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <div class="nav-center desktop-only">
            <div class="search-bar">
                <i class="fas fa-search search-icon"></i>
                <input type="text" placeholder="Cari produk di PAS Market..." data-home-search>
                <button class="search-btn" type="button">Cari</button>
                <div class="category-dropdown" id="navbar-category-dropdown" style="display:none">
                    <ul class="category-dropdown-list" data-navbar-categories></ul>
                </div>
            </div>
        </div>
        <div class="nav-right">
            <a href="#home" class="nav-link active" data-screen="home">Beranda</a>
            <a href="#brand" class="nav-link" data-screen="brand">Official Brand</a>
            <a href="#qorder" class="nav-link" data-screen="qorder">Q.Order</a>
            <a href="#info" class="nav-link" data-screen="info">Informasi</a>
            <a href="#productList" class="nav-link" data-action="openProductList">Promo</a>
            <button class="nav-action desktop-only" id="akun-desktop-btn" type="button" onclick="document.getElementById('akun-btn').click()">
                <i class="far fa-user"></i>
                <span>Akun</span>
            </button>
            <button class="nav-action" id="cart-btn" type="button">
                <div class="icon-wrapper">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge-dot" data-cart-badge style="display: none;">0</span>
                </div>
                <span class="desktop-only">Keranjang</span>
            </button>
        </div>
    </div>
</header>
