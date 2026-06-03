<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="PAS Market - Platform penjualan produk terpercaya">
    <meta name="keywords" content="pas market, e-commerce, produk, belanja online">
    <meta name="theme-color" content="#003366">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>@yield('title', 'PAS Market - Belanja Online Terpercaya')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('guest/css/app.css') }}?v={{ filemtime(public_path('guest/css/app.css')) }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100 has-bottom-nav" @if(Request::is('login', 'register')) data-spa="false" @endif>

    {{-- Sales Customer Select Modal (for add-to-cart) --}}
    @if(Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())
    <div class="modal fade" id="salesCustomerModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:14px;">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Pilih Customer</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Produk akan ditambahkan ke keranjang customer yang dipilih.</p>
                    <div class="mb-2">
                        <input type="text" id="salesCustSearch" class="form-control form-control-sm" placeholder="Cari customer...">
                    </div>
                    <div class="list-group" id="salesCustList" style="max-height:260px;overflow-y:auto;">
                        <div class="text-center text-muted py-3 small">Memuat...</div>
                    </div>
                    <div class="mt-2 d-none" id="salesCustEmpty">
                        <p class="text-muted small mb-0 text-center">Tidak ada customer ditemukan.</p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Desktop Header (hidden on mobile) -->
    <header class="desktop-header navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top d-none d-lg-flex">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ url('/') }}">
                <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
            </a>

            <div class="mx-auto" style="width: 50%;">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Cari produk..." id="searchInput">
                    <button class="btn btn-primary" type="button" id="searchBtn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/categories') }}">Kategori</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/products') }}">Produk</a></li>
                <li class="nav-item">
                    <a class="nav-link position-relative" href="{{ (Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())) ? url('/cart') : (url('/login').'?redirect='.urlencode('/cart')) }}">
                        <i class="bi bi-cart3 fs-5"></i>
                        <span class="badge rounded-pill bg-danger nav-cart-badge" id="cartCountDesktop" style="display: none;">0</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ (Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())) ? url('/profile') : (url('/login').'?redirect='.urlencode('/profile')) }}">
                        <i class="bi bi-person fs-5"></i>
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <!-- Mobile Top Bar -->
    <div class="mobile-topbar d-lg-none">
        <div class="mobile-topbar-inner">
            @section('mobile-topbar-inner')
            <a class="mobile-logo" href="{{ url('/') }}">
                <span class="pas-brand-text">PAS</span><span class="pas-brand-sub">Market</span>
            </a>
            <div class="mobile-topbar-actions">
                <button class="mobile-search-toggle" id="mobileSearchToggle" aria-label="Cari">
                    <i class="bi bi-search"></i>
                </button>
                <a href="{{ url('/cart') }}" class="mobile-cart-link" id="cartBtnMobile" aria-label="Keranjang">
                    <i class="bi bi-cart3"></i>
                    <span class="cart-badge-mobile" id="cartCountMobile" style="display: none;">0</span>
                </a>
            </div>
            @show
        </div>
        <!-- Mobile Search (expandable) -->
        <div class="mobile-search-bar d-none" id="mobileSearchBar">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Cari produk..." id="searchInputMobile">
                <button class="btn btn-primary" type="button" id="searchBtnMobile">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="bottom-nav d-lg-none" id="bottomNav">
        <a href="{{ url('/') }}" class="bottom-nav-item {{ Request::is('/') ? 'active' : '' }}">
            <i class="bi bi-house-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ url('/categories') }}" class="bottom-nav-item {{ Request::is('categories') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i>
            <span>Kategori</span>
        </a>
        <a href="{{ url('/products') }}" class="bottom-nav-item {{ Request::is('products') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i>
            <span>Produk</span>
        </a>
        <a href="{{ (Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())) ? url('/cart') : (url('/login').'?redirect='.urlencode('/cart')) }}"
           class="bottom-nav-item {{ Request::is('cart') ? 'active' : '' }}">
            <i class="bi bi-cart-fill"></i>
            <span>Keranjang</span>
            <span class="bottom-nav-badge" id="cartCountBottom" style="display: none;">0</span>
        </a>
        <a href="{{ (Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())) ? url('/profile') : (url('/login').'?redirect='.urlencode('/profile')) }}"
           class="bottom-nav-item {{ Request::is('profile*') ? 'active' : '' }}">
            <i class="bi bi-person-fill"></i>
            <span>Akun</span>
        </a>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1 main-content">
        <div class="container mt-2 mt-lg-3 px-3 px-lg-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mobile-alert" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm mobile-alert" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show shadow-sm mobile-alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show shadow-sm mobile-alert" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm mobile-alert" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer (hidden on mobile) -->
    <footer class="bg-dark text-white py-5 mt-auto d-none d-lg-block">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-shop"></i> PAS Market</h5>
                    <p class="text-light">Platform penjualan produk terpercaya dengan kualitas terjamin dan harga terbaik.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp fs-4"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Kategori</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/products') }}" class="text-light text-decoration-none">Elektronik</a></li>
                        <li><a href="{{ url('/products') }}" class="text-light text-decoration-none">Fashion</a></li>
                        <li><a href="{{ url('/products') }}" class="text-light text-decoration-none">Kebutuhan Rumah</a></li>
                        <li><a href="{{ url('/products') }}" class="text-light text-decoration-none">Olahraga</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Layanan</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/contact') }}" class="text-light text-decoration-none">Bantuan</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-light text-decoration-none">Pengembalian</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-light text-decoration-none">Pengaduan</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-light text-decoration-none">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Tentang</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/about') }}" class="text-light text-decoration-none">Tentang Kami</a></li>
                        <li><a href="{{ url('/about') }}" class="text-light text-decoration-none">Karir</a></li>
                        <li><a href="{{ url('/about') }}" class="text-light text-decoration-none">Blog</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-light text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 mb-4">
                    <h6 class="fw-bold mb-3">Hubungi Kami</h6>
                    <ul class="list-unstyled">
                        <li class="text-light"><i class="bi bi-geo-alt"></i> Jakarta, Indonesia</li>
                        <li class="text-light"><i class="bi bi-telephone"></i> +62 812-3456-7890</li>
                        <li class="text-light"><i class="bi bi-envelope"></i> info@pasmarket.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4 bg-secondary">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-light">&copy; 2024 PAS Market. Hak Cipta Dilindungi.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <img src="{{ asset('guest/img/placeholder-payment.svg') }}" alt="Payment Methods" class="img-fluid" style="max-height: 30px;">
                </div>
            </div>
        </div>
    </footer>

    <!-- Loading Indicators -->
    <div id="global-loading-overlay" style="display: none;">
        <div class="text-center">
            <div class="loading-spinner mb-3"></div>
            <div class="loading-text">Memuat data...</div>
            <div class="loading-sub">Mohon tunggu sebentar</div>
        </div>
    </div>
    <div id="page-loading-indicator" style="display: none;"></div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Core Application JS -->
    <script src="{{ asset('guest/js/app.js?v='.(@filemtime(public_path('guest/js/app.js')) ?: time())) }}"></script>
    <script src="{{ asset('guest/js/router.js') }}"></script>
    <script src="{{ asset('guest/js/products.js?v='.(@filemtime(public_path('guest/js/products.js')) ?: time())) }}"></script>
    <script src="{{ asset('guest/js/search.js') }}"></script>
    <script src="{{ asset('guest/js/loading.js') }}"></script>

    @stack('scripts')
    <script>
        window.PAS = window.PAS || {};
        window.PAS.auth = {
            loggedIn: {{ (Auth::guard('customer')->check() || (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales())) ? 'true' : 'false' }},
            isSales: {{ (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales()) ? 'true' : 'false' }},
            user: @json(Auth::guard('customer')->user() ?? (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales() ? Auth::guard('web')->user() : null)),
            loginUrl: '{{ route('guest.login') }}'
        };
        window.PAS.urls = {
            sync: '{{ url('/api/guest/sync') }}',
            home: '{{ url('/api/guest/home') }}',
            orders: '{{ url('/api/guest/orders') }}',
            products: '{{ url('/api/guest/products') }}',
            productShow: '{{ url('/api/guest/products') }}',
            cart: '{{ route('guest.cart.index') }}',
            myCustomers: '{{ route('guest.cart.my-customers') }}'
        };
        window.PAS.initialScreen = '{{ $initialScreen ?? 'home' }}';
        window.PAS.initialProductId = '{{ $initialProductId ?? '' }}';

        // Mobile search toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobileSearchToggle');
            const bar = document.getElementById('mobileSearchBar');
            if (toggle && bar) {
                toggle.addEventListener('click', function() {
                    bar.classList.toggle('d-none');
                    if (!bar.classList.contains('d-none')) {
                        bar.querySelector('input').focus();
                    }
                });
            }
        });
    </script>
</body>
</html>
