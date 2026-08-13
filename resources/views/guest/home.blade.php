@extends('guest.layouts.app')

@section('title', 'PAS Market - Belanja Online Terpercaya')

@section('content')
@php
    $heroImages = $broadcasts ?? collect();

    $categoryIcons = ['laptop', 'bag', 'house', 'bicycle', 'heart', 'car', 'phone', 'watch', 'camera', 'speaker', 'tools', 'tags'];
@endphp

@push('styles')
<script>document.documentElement.classList.add('js');</script>
@endpush

<div class="home-page">

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <div class="hero-banner-frame">
                <div class="hero-banner-media">
                    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">

                        <div class="carousel-inner">
                            @forelse($heroImages as $index => $broadcast)
                                @php
                                    $heroImageUrl = \Illuminate\Support\Str::startsWith($broadcast->image_path, ['http://', 'https://'])
                                        ? $broadcast->image_path
                                        : asset('storage/' . $broadcast->image_path);
                                @endphp

                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img
                                        src="{{ $heroImageUrl }}"
                                        alt="PAS Market"
                                        class="hero-banner-image"
                                        onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-banner.svg') }}'"
                                    >
                                </div>

                            @empty
                                <div class="carousel-item active">
                                    <img
                                        src="{{ asset('guest/img/placeholder-banner.svg') }}"
                                        class="hero-banner-image"
                                        alt="Placeholder"
                                    >
                                </div>
                            @endforelse
                        </div>

                    </div>
                    <div class="hero-banner-chip" aria-hidden="true">
                        <i class="bi bi-shop"></i>
                        <span>PAS Market</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Market Ticker -->
    <div class="market-ticker" aria-label="Kategori unggulan">
        <div class="market-ticker-track" id="marketTickerTrack">
            @forelse(($categories ?? collect()) as $category)
                <a class="ticker-item" href="{{ url('/products') }}?category_id={{ $category->category_code }}">
                    <i class="bi bi-dot"></i><span>{{ $category->name }}</span>
                </a>
            @empty
                <a class="ticker-item" href="{{ url('/products') }}"><i class="bi bi-dot"></i><span>Jelajahi Produk</span></a>
                <a class="ticker-item" href="{{ url('/categories') }}"><i class="bi bi-dot"></i><span>Semua Kategori</span></a>
                <a class="ticker-item" href="{{ url('/products') }}"><i class="bi bi-dot"></i><span>Promo Spesial</span></a>
            @endforelse
            <a class="ticker-cta" href="{{ url('/products') }}">Belanja Sekarang</a>
        </div>
    </div>

    <!-- Promo Banner -->
    <section class="py-4 py-lg-5">
        <div class="container">
            <div class="home-banner reveal">
                <img
                    src="{{ asset('guest/img/bannerrrrr.png') }}"
                    alt="Promo PAS Market"
                    class="home-banner-img"
                    loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-banner.svg') }}'"
                >
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <div class="section-container reveal">
                <h3 class="section-title">Kategori</h3>

                <!-- Desktop & tablet: larger grid, no slide -->
                <div class="cat-grid cat-grid-desktop">
                    @foreach(($categories ?? collect())->take(10) as $category)
                        <a href="{{ url('/products') }}?category_id={{ $category->category_code }}" class="cat-item" data-category-id="{{ $category->category_code }}">
                            <i class="bi bi-{{ $categoryIcons[$loop->index % count($categoryIcons)] ?? 'tags' }} cat-icon"></i>
                            <span class="cat-name">{{ $category->name }}</span>
                        </a>
                    @endforeach
                    @if(($categories ?? collect())->count() > 10)
                        <a href="{{ url('/categories') }}" class="cat-see-all">
                            <i class="bi bi-grid-3x3-gap-fill cat-icon"></i>
                            <span class="cat-name">Lihat Semua</span>
                        </a>
                    @endif
                </div>

                <!-- Mobile: compact grid, no slide -->
                <div class="cat-grid cat-grid-mobile">
                    @foreach(($categories ?? collect())->take(6) as $category)
                        <a href="{{ url('/products') }}?category_id={{ $category->category_code }}" class="cat-item" data-category-id="{{ $category->category_code }}">
                            <i class="bi bi-{{ $categoryIcons[$loop->index % count($categoryIcons)] ?? 'tags' }} cat-icon"></i>
                            <span class="cat-name">{{ $category->name }}</span>
                        </a>
                    @endforeach
                    @if(($categories ?? collect())->count() > 6)
                        <a href="{{ url('/categories') }}" class="cat-see-all">
                            <i class="bi bi-grid-3x3-gap-fill cat-icon"></i>
                            <span class="cat-name">Lihat Semua</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if(($featuredProducts ?? collect())->isNotEmpty())
    <section class="py-5">
        <div class="container">
            <div class="section-container reveal">
                <div class="section-header">
                    <h3 class="section-title">Produk Terlaris</h3>
                    <a href="{{ url('/products') }}" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="products-scroll">
                    @foreach($featuredProducts as $product)
                        @include('guest.partials.product-card-item')
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    @if(($newProducts ?? collect())->isNotEmpty())
    <section class="py-5 bg-light">
        <div class="container">
            <div class="section-container reveal">
                <div class="section-header">
                    <h3 class="section-title">Produk Terbaru</h3>
                    <a href="{{ url('/products') }}" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="products-scroll">
                    @foreach($newProducts as $product)
                        @include('guest.partials.product-card-item')
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Product Sections by Status -->
    @foreach($statusProducts as $item)
    <section class="py-5 {{ $loop->even ? 'bg-light' : '' }}">
        <div class="container">
            <div class="section-container reveal">
                <div class="section-header">
                    <h3 class="section-title">{{ $item['status']->name }}</h3>
                    <a href="{{ url('/products') }}" class="see-all">Lihat semua <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="products-scroll">
                    @foreach($item['products'] as $product)
                        @include('guest.partials.product-card-item')
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
                <h2 class="features-heading">Mengapa Memilih PAS Market?</h2>
            </div>

            <div class="row g-4 reveal">
                <div class="col-md-4">
                    <div class="feature-tile">
                        <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                        <h5 class="fw-bold mb-3">100% Aman &amp; Terpercaya</h5>
                        <p class="text-muted mb-0">Transaksi aman dengan sistem pembayaran terpercaya dan perlindungan pembeli.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-tile">
                        <div class="feature-icon"><i class="bi bi-lightning"></i></div>
                        <h5 class="fw-bold mb-3">Pengiriman Cepat</h5>
                        <p class="text-muted mb-0">Pengiriman cepat ke seluruh Indonesia dengan berbagai pilihan kurir.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-tile">
                        <div class="feature-icon"><i class="bi bi-headset"></i></div>
                        <h5 class="fw-bold mb-3">Layanan Pelanggan 24/7</h5>
                        <p class="text-muted mb-0">Tim customer service siap membantu kapan pun Anda butuhkan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-5">
        <div class="container">
            <div class="newsletter-band reveal">
                <div class="row justify-content-center">
                    <div class="col-lg-7 text-center">
                        <span class="newsletter-eyebrow">PAS Market</span>
                        <h2 class="newsletter-title">Dapatkan Promo Spesial!</h2>
                        <p class="newsletter-sub">Langganan newsletter kami untuk mendapatkan promo dan diskon spesial setiap minggunya.</p>
                        <form id="newsletterForm" class="newsletter-form">
                            <input type="email" class="form-control" placeholder="Masukkan email Anda" aria-label="Alamat email" required>
                            <button type="submit" class="btn">Langganan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.documentElement;
    root.classList.add('js');
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Scroll reveal (opacity + transform only, ease-out, once)
    var reveals = document.querySelectorAll('.home-page .reveal');
    if (reduce || !('IntersectionObserver' in window)) {
        reveals.forEach(function (el) { el.classList.add('is-in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-in');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    }

    // Market ticker: duplicate content so the marquee loops seamlessly
    var track = document.getElementById('marketTickerTrack');
    if (track) {
        var once = track.innerHTML;
        if (once && !reduce) {
            track.innerHTML = once;
            var unit = track.scrollWidth || 1;
            var vw = window.innerWidth || 1200;
            var copies = Math.max(2, Math.ceil((vw * 1.5) / unit));
            if (copies % 2 !== 0) { copies++; }
            track.innerHTML = once.repeat(copies);
            track.style.animationDuration = Math.max(16, (track.scrollWidth / 2) / 55) + 's';
            track.classList.add('is-animating');
        }
    }

    // Newsletter form
    var newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var input = this.querySelector('input[type="email"]');
            var email = input && input.value.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                if (window.PAS && PAS.Cart) { PAS.Cart.showNotification('Masukkan alamat email yang valid.', 'warning'); }
                return;
            }
            if (window.PAS && PAS.Cart) { PAS.Cart.showNotification('Terima kasih! Anda berhasil berlangganan newsletter.', 'success'); }
            this.reset();
        });
    }
})();
</script>
@endpush
