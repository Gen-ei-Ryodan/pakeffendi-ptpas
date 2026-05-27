@extends('guest.layouts.app')

@section('title', 'Semua Kategori - PAS Market')

@section('content')
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb" class="mobile-hide">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kategori</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mt-3 mobile-hide">
            <h1 class="h3 fw-bold text-secondary mb-0">Semua Kategori</h1>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <div class="row g-2 g-lg-4">
            @foreach(($categories ?? collect()) as $category)
                @php
                    $imagePath = $category->image_path;
                    $imageUrl = $imagePath
                        ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/' . $imagePath))
                        : asset('guest/img/placeholder-product.svg');
                @endphp
                <div class="col-6 col-md-4 col-lg-3 cat-mobile-col">
                    <a href="{{ url('/products') }}?category_id={{ $category->category_code }}" class="text-decoration-none">
                        <div class="category-tile cat-mobile-card">
                            <div class="category-tile-media cat-media-mobile">
                                <img src="{{ $imageUrl }}" alt="{{ $category->name }}" class="category-tile-image" onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                            </div>
                            <div class="category-tile-title cat-title-mobile">
                                {{ $category->name }}
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
