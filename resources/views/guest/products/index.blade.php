@extends('guest.layouts.app')

@section('title', 'Semua Produk - PAS Market')

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Produk</li>
            </ol>
        </nav>
        
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-3 gap-2">
            <h1 class="h3 fw-bold text-secondary mb-0 products-page-title">Semua Produk</h1>
            <div class="d-flex gap-2 products-header-actions">
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

<!-- Filter Sidebar -->
<div class="collapse d-lg-none" id="filterSidebar">
    <div class="container py-3 border-bottom">
        <form method="GET" action="{{ url('/products') }}" class="row g-3">
            <input type="hidden" name="sort" value="{{ $filters['sort'] ?? request('sort') }}">
            <div class="col-12">
                <label class="form-label fw-semibold">Cari</label>
                <input class="form-control form-control-sm" name="q" value="{{ $filters['q'] ?? request('q') }}" placeholder="Nama produk / SKU">
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Kategori</label>
                <select class="form-select form-select-sm" name="category_id">
                    <option value="">Semua Kategori</option>
                    @foreach(($categories ?? collect()) as $cat)
                        <option value="{{ $cat->category_code }}" @selected((string) ($filters['category_id'] ?? request('category_id')) === (string) $cat->category_code)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold">Brand</label>
                <select class="form-select form-select-sm" name="brand_id">
                    <option value="">Semua Brand</option>
                    @foreach(($brands ?? collect()) as $brand)
                        <option value="{{ $brand->brand_code }}" @selected((string) ($filters['brand_id'] ?? request('brand_id')) === (string) $brand->brand_code)>{{ $brand->brand_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary btn-sm w-100" type="submit">Terapkan</button>
            </div>
        </form>
    </div>
</div>

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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="text-muted mb-0">
                        @if(isset($products) && $products->total() > 0)
                            Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }} dari {{ $products->total() }} produk
                        @else
                            Tidak ada produk
                        @endif
                    </p>
                </div>
                
                <!-- Products Grid -->
                <div class="row g-3 g-lg-4">
                    @foreach(($products ?? collect()) as $product)
                    @php
                        $imageUrl = $product->photo_url;
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card" data-product-id="{{ $product->id }}">
                            <div class="position-relative">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="product-image">
                            </div>
                            
                            <div class="product-info">
                                <div class="mb-2">
                                    <span class="text-muted small">{{ $product->brand?->brand_name }}</span>
                                </div>
                                <h6 class="product-title text-truncate-2">{{ $product->name }}</h6>
                                @if(($product->variant ?? '') !== '')
                                    <div class="text-muted small text-truncate">{{ $product->variant }}</div>
                                @endif

                                @foreach($product->pricing_tiers as $tier)
                                <div class="d-flex justify-content-between align-items-center small py-0">
                                    @if($tier['qty_end'])
                                        <span class="text-muted">{{ $tier['qty_start'] }} - {{ $tier['qty_end'] }} pcs</span>
                                    @else
                                        <span class="text-muted">{{ $tier['qty_start'] }}+ pcs</span>
                                    @endif
                                    <span class="product-price">Rp {{ number_format((float) $tier['price'], 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                
                                <div class="d-flex justify-content-end mt-2">
                                    <button class="btn btn-primary btn-sm btn-add-to-cart product-cart-btn" data-product-id="{{ $product->id }}">
                                        <i class="bi bi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if(isset($products))
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $products->links('pagination::bootstrap-5') }}
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
});
</script>
@endpush
