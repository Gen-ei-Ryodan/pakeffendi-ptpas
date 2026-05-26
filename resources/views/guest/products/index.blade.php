@extends('guest.layouts.app')

@section('title', 'Semua Produk - PAS Market')

@section('mobile-topbar-inner')
<div class="mobile-prod-topbar-inner">
    <div class="search-wrap">
        <i class="bi bi-search search-ico"></i>
        <input type="text" placeholder="Cari produk..." id="mobileProdSearch">
    </div>
    <button class="topbar-btn" type="button" id="mobileSortBtn"><i class="bi bi-arrow-up-short"></i></button>
    <button class="topbar-btn" type="button" id="mobileFilterBtn"><i class="bi bi-sliders"></i></button>
</div>
@endsection

@section('content')
<!-- Page Header -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb" class="mobile-hide">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
        
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-3 gap-2 mobile-hide">
            <h1 class="h3 fw-bold text-secondary mb-0 products-page-title">Semua Produk</h1>
            <div class="d-flex gap-2 products-header-actions d-none d-lg-flex">
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterSidebar">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <select class="form-select form-select-sm" id="sortSelect" name="sort" style="width: auto;">
                    <option value="">Urutkan</option>
                    <option value="price-asc" @selected(($filters['sort'] ?? request('sort')) === 'price-asc')>Harga: Rendah ke Tinggi</option>
                    <option value="price-desc" @selected(($filters['sort'] ?? request('sort')) === 'price-desc')>Harga: Tinggi ke Rendah</option>
                    <option value="name-asc" @selected(($filters['sort'] ?? request('sort')) === 'name-asc')>Nama: A-Z</option>
                    <option value="name-desc" @selected(($filters['sort'] ?? request('sort')) === 'name-desc')>Nama: Z-A</option>
                    <option value="newest" @selected(($filters['sort'] ?? request('sort')) === 'newest')>Terbaru</option>
                    <option value="popular" @selected(($filters['sort'] ?? request('sort')) === 'popular')>Terlaris</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-4">
    <div class="container">
        <div class="row">
            <!-- Desktop Sidebar -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Filter Produk</h5>
                        <form method="GET" action="{{ url('/products') }}">
                            <input type="hidden" name="sort" value="{{ $filters['sort'] ?? request('sort') }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Cari</label>
                                <input class="form-control form-control-sm" name="q" value="{{ $filters['q'] ?? request('q') }}" placeholder="Nama produk / SKU">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori</label>
                                <select class="form-select form-select-sm" name="category_id">
                                    <option value="">Semua Kategori</option>
                                    @foreach(($categories ?? collect()) as $cat)
                                        <option value="{{ $cat->category_code }}" @selected((string) ($filters['category_id'] ?? request('category_id')) === (string) $cat->category_code)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Brand</label>
                                <select class="form-select form-select-sm" name="brand_id">
                                    <option value="">Semua Brand</option>
                                    @foreach(($brands ?? collect()) as $brand)
                                        <option value="{{ $brand->brand_code }}" @selected((string) ($filters['brand_id'] ?? request('brand_id')) === (string) $brand->brand_code)>{{ $brand->brand_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-primary w-100" type="submit">Terapkan</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Product Grid -->
            <div class="col-lg-9">
                <!-- Results Info -->
                <div class="d-flex justify-content-between align-items-center mb-4 mobile-hide">
                    <p class="text-muted mb-0">
                        @if(isset($products) && $products->total() > 0)
                            Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }} dari {{ $products->total() }} produk
                        @else
                            Tidak ada produk
                        @endif
                    </p>
                </div>
                
                <!-- Products Grid -->
                <div class="products-grid-manual" id="productsGrid">
                    @foreach(($products ?? collect()) as $product)
                        @include('guest.partials.product-card-item')
                    @endforeach
                </div>

                <!-- Load More Trigger -->
                @if(isset($products) && $products->hasMorePages())
                    <div class="products-load-more d-lg-none" id="productsLoadMore">
                        <div class="load-more-spinner" id="loadMoreSpinner" style="display:none;">
                            <div class="spinner"></div>
                            <span>Memuat produk...</span>
                        </div>
                        <div class="load-more-end" id="loadMoreEnd" style="display:none;">
                            <span>Semua produk telah dimuat</span>
                        </div>
                    </div>
                @endif
                
                <!-- Pagination (Desktop) -->
                @if(isset($products))
                    <div class="mt-5 d-flex justify-content-center d-none d-lg-flex">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Mobile Sort Sheet -->
<div class="sort-sheet-overlay d-lg-none" id="sortSheetOverlay"></div>
<div class="sort-sheet d-lg-none" id="sortSheet">
    <div class="sort-handle"></div>
    <div class="sort-title">Urutkan</div>
    <div class="sort-option {{ (!request('sort') || request('sort') === '') ? 'active' : '' }}" data-sort="">
        <div class="check"></div>Paling Sesuai
    </div>
    <div class="sort-option {{ request('sort') === 'price-asc' ? 'active' : '' }}" data-sort="price-asc">
        <div class="check"></div>Harga: Rendah ke Tinggi
    </div>
    <div class="sort-option {{ request('sort') === 'price-desc' ? 'active' : '' }}" data-sort="price-desc">
        <div class="check"></div>Harga: Tinggi ke Rendah
    </div>
    <div class="sort-option {{ request('sort') === 'name-asc' ? 'active' : '' }}" data-sort="name-asc">
        <div class="check"></div>Nama: A-Z
    </div>
    <div class="sort-option {{ request('sort') === 'name-desc' ? 'active' : '' }}" data-sort="name-desc">
        <div class="check"></div>Nama: Z-A
    </div>
    <div class="sort-option {{ request('sort') === 'newest' ? 'active' : '' }}" data-sort="newest">
        <div class="check"></div>Terbaru
    </div>
    <div class="sort-option {{ request('sort') === 'popular' ? 'active' : '' }}" data-sort="popular">
        <div class="check"></div>Terlaris
    </div>
</div>

<!-- Mobile Filter Sheet -->
<div class="filter-sheet-overlay d-lg-none" id="filterSheetOverlay"></div>
<div class="filter-sheet d-lg-none" id="filterSheet">
    <div class="filter-handle"></div>
    <div class="filter-title">Filter Produk</div>
    <form method="GET" action="{{ url('/products') }}">
        <input type="hidden" name="sort" value="{{ request('sort') }}">
        <div class="mb-3">
            <div class="filter-label">Cari</div>
            <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nama produk / SKU">
        </div>
        <div class="mb-3">
            <div class="filter-label">Kategori</div>
            <select class="form-select" name="category_id">
                <option value="">Semua Kategori</option>
                @foreach(($categories ?? collect()) as $cat)
                    <option value="{{ $cat->category_code }}" @selected((string) request('category_id') === (string) $cat->category_code)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <div class="filter-label">Brand</div>
            <select class="form-select" name="brand_id">
                <option value="">Semua Brand</option>
                @foreach(($brands ?? collect()) as $brand)
                    <option value="{{ $brand->brand_code }}" @selected((string) request('brand_id') === (string) $brand->brand_code)>{{ $brand->brand_name }}</option>
                @endforeach
            </select>
        </div>
        <button class="filter-apply-btn" type="submit">Terapkan</button>
        <button class="filter-reset-btn" type="button" id="mobileFilterReset">Reset</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForms = document.querySelectorAll('form[method="GET"][action$="/products"]');
    filterForms.forEach(form => {
        form.addEventListener('change', function(e) {
            const target = e.target;
            if (!(target instanceof HTMLElement)) return;
            if (target.matches('input[name="q"], select[name="category_id"], select[name="brand_id"]')) {
                form.submit();
            }
        });
    });

    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            if (sortSelect.value) {
                params.set('sort', sortSelect.value);
            } else {
                params.delete('sort');
            }
            window.location.search = params.toString();
        });
    }

    // Mobile: search input enter/submit
    const mobileSearch = document.getElementById('mobileProdSearch');
    if (mobileSearch) {
        let searchTimer;
        mobileSearch.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const params = new URLSearchParams(window.location.search);
                if (this.value.trim()) {
                    params.set('q', this.value.trim());
                } else {
                    params.delete('q');
                }
                window.location.search = params.toString();
            }, 500);
        });
    }

    // Mobile: sort sheet
    const sortBtn = document.getElementById('mobileSortBtn');
    const sortSheet = document.getElementById('sortSheet');
    const sortOverlay = document.getElementById('sortSheetOverlay');
    if (sortBtn && sortSheet) {
        sortBtn.addEventListener('click', function() {
            sortSheet.classList.add('show');
            sortOverlay.classList.add('show');
        });
        sortOverlay.addEventListener('click', function() {
            sortSheet.classList.remove('show');
            sortOverlay.classList.remove('show');
        });
        sortSheet.querySelectorAll('.sort-option').forEach(function(opt) {
            opt.addEventListener('click', function() {
                const sortVal = this.dataset.sort;
                const params = new URLSearchParams(window.location.search);
                if (sortVal) {
                    params.set('sort', sortVal);
                } else {
                    params.delete('sort');
                }
                window.location.search = params.toString();
            });
        });
    }

    // Mobile: filter sheet
    const filterBtn = document.getElementById('mobileFilterBtn');
    const filterSheet = document.getElementById('filterSheet');
    const filterOverlay = document.getElementById('filterSheetOverlay');
    if (filterBtn && filterSheet) {
        filterBtn.addEventListener('click', function() {
            filterSheet.classList.add('show');
            filterOverlay.classList.add('show');
        });
        filterOverlay.addEventListener('click', function() {
            filterSheet.classList.remove('show');
            filterOverlay.classList.remove('show');
        });
    }

    // Mobile: filter reset
    const resetBtn = document.getElementById('mobileFilterReset');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            window.location.href = '{{ url('/products') }}';
        });
    }

    // Mobile: infinite scroll
    const grid = document.getElementById('productsGrid');
    const loadMore = document.getElementById('productsLoadMore');
    if (grid && loadMore) {
        let loading = false;
        let currentPage = 2;
        let hasMore = true;
        let scrollTimer;

        const loadNextPage = function() {
            if (loading || !hasMore) return;
            loading = true;

            const spinner = document.getElementById('loadMoreSpinner');
            if (spinner) spinner.style.display = 'flex';

            const params = new URLSearchParams(window.location.search);
            params.set('page', currentPage);

            fetch('{{ url('/products/load-more') }}?' + params.toString(), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.html) {
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    while (temp.firstChild) {
                        grid.appendChild(temp.firstChild);
                    }
                }
                hasMore = data.hasMore;
                currentPage = data.nextPage;

                if (spinner) spinner.style.display = 'none';

                if (!hasMore) {
                    const endMsg = document.getElementById('loadMoreEnd');
                    if (endMsg) endMsg.style.display = 'block';
                }

                loading = false;
            })
            .catch(function() {
                if (spinner) spinner.style.display = 'none';
                loading = false;
            });
        };

        const onScroll = function() {
            if (!hasMore || loading) return;
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(function() {
                var rect = loadMore.getBoundingClientRect();
                if (rect.top < window.innerHeight + 200) {
                    loadNextPage();
                }
            }, 150);
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        // Initial check
        setTimeout(onScroll, 300);
    }
});
</script>
@endpush
