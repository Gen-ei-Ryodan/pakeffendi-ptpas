@extends('guest.layouts.app')

@section('title', 'PAS Market - Belanja Online Terpercaya')

@section('content')
@php
    $heroImagePath = $broadcasts?->first()?->image_path;
    $heroImageUrl = $heroImagePath
        ? (\Illuminate\Support\Str::startsWith($heroImagePath, ['http://', 'https://']) ? $heroImagePath : asset('storage/' . $heroImagePath))
        : asset('guest/img/placeholder-banner.svg');
    $categoryIcons = ['laptop', 'bag', 'house', 'bicycle', 'heart', 'car', 'phone', 'watch', 'camera', 'speaker', 'tools', 'tags'];
    $categoryColors = ['blue', 'orange', 'purple', 'yellow', 'cyan', 'grey'];
@endphp
<!-- Hero Banner -->
<section class="hero-banner">
    <div class="container">
        <div class="hero-banner-frame">
            <div class="hero-banner-media">
                <img src="{{ $heroImageUrl }}" alt="PAS Market" class="hero-banner-image">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="section-container">
            <h3 class="section-title">Kategori</h3>
            <div class="categories-scroll">
                @foreach(($categories ?? collect()) as $category)
                    <div class="category-card {{ $categoryColors[$loop->index % count($categoryColors)] }}" role="button" tabindex="0" data-category-id="{{ $category->category_code }}">
                        <i class="bi bi-{{ $categoryIcons[$loop->index % count($categoryIcons)] ?? 'tags' }} cat-icon"></i>
                        <span class="cat-name">{{ $category->name }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<!-- Product Sections by Status -->
@foreach($statusProducts as $item)
<section class="py-5 {{ $loop->even ? 'bg-light' : '' }}">
    <div class="container">
        <div class="section-container">
            <div class="section-header">
                <h3 class="section-title">{{ $item['status']->name }}</h3>
                <a href="{{ url('/products') }}" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
            </div>
            <div class="products-scroll">
                @foreach($item['products'] as $product)
                    @php
                        $imageUrl = $product->photo_url;
                    @endphp
                    <div class="product-card" data-product-id="{{ $product->id }}">
                        <div class="prod-img-box">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22400%22%3E%3Crect width=%22400%22 height=%22400%22 fill=%22%23f8f9fa%22/%3E%3C/svg%3E'">
                        </div>
                        <div class="prod-info">
                            <p class="prod-brand">{{ $product->brand?->brand_name }}</p>
                            <p class="prod-name text-truncate-2">{{ $product->name }}</p>
                            @if(($product->variant ?? '') !== '')
                                <p class="text-muted small mb-1">{{ $product->variant }}</p>
                            @endif
                            <div class="pricing-tiers">
                                <p class="prod-price">Rp {{ number_format((float) ($product->pricing_tiers[0]['net_price'] ?? $product->price_1), 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endforeach

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold text-secondary">Mengapa Memilih PAS Market?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-shield-check display-4 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-3">100% Aman & Terpercaya</h5>
                <p class="text-muted">Transaksi aman dengan sistem pembayaran terpercaya dan perlindungan pembeli.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-lightning display-4 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-3">Pengiriman Cepat</h5>
                <p class="text-muted">Pengiriman cepat ke seluruh Indonesia dengan berbagai pilihan kurir.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-headset display-4 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-3">Layanan Pelanggan 24/7</h5>
                <p class="text-muted">Tim customer service siap membantu kapan pun Anda butuhkan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold text-secondary mb-3">Dapatkan Promo Spesial!</h2>
                <p class="text-muted mb-4">Langganan newsletter kami untuk mendapatkan promo dan diskon spesial setiap minggunya.</p>
                <form class="d-flex flex-column flex-md-row gap-2 justify-content-center" style="max-width: 500px; margin: 0 auto;">
                    <input type="email" class="form-control form-control-lg" placeholder="Masukkan email Anda">
                    <button type="submit" class="btn btn-primary btn-lg px-4">Langganan</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Additional home page specific scripts
document.addEventListener('DOMContentLoaded', function() {
    // Category card clicks
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('click', function() {
            const categoryName = this.querySelector('.category-name').textContent;
            PAS.Cart.showNotification(`Memuat kategori: ${categoryName}`, 'info');
        });
    });
    
    // Newsletter form
    const newsletterForm = document.querySelector('form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                PAS.Cart.showNotification('Terima kasih! Anda berhasil berlangganan newsletter.', 'success');
                this.reset();
            }
        });
    }
});
</script>
@endpush
