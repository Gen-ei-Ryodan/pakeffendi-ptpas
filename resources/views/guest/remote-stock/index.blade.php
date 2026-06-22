@extends('guest.layouts.app')

@section('title', 'Remote Stock - PAS Market')

@push('styles')
<style>
    .connection-card { border-left: 4px solid #dee2e6; }
    .connection-card.success { border-left-color: #198754; }
    .connection-card.error { border-left-color: #dc3545; }
    .stock-match { background-color: #d1e7dd; }
    .stock-match:hover { background-color: #b8dbc8; }
    .stock-nomatch { background-color: #f8f9fa; }
    .stock-nomatch:hover { background-color: #e9ecef; }
    .search-box { max-width: 400px; }
    .pagination-info { font-size: 0.875rem; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="/" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="fw-bold mb-0">Remote Stock Lookup</h5>
    </div>

    {{-- Connection Status --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <h6 class="fw-bold mb-3">
                <i class="bi bi-database me-2"></i>Koneksi Remote DB (EzSystem)
            </h6>
            <div class="connection-card {{ $connection['success'] ? 'success' : 'error' }} p-3 rounded-3">
                @if($connection['success'])
                    <div class="text-success fw-semibold mb-1">
                        <i class="bi bi-check-circle-fill me-1"></i>Terhubung!
                    </div>
                    <small class="text-muted">
                        SQL Server 2005 &middot;
                        <strong>{{ number_format($connection['total_stock_records']) }}</strong> records di vwtotalqtystock
                    </small>
                @else
                    <div class="text-danger fw-semibold mb-1">
                        <i class="bi bi-x-circle-fill me-1"></i>Gagal Terhubung
                    </div>
                    <small class="text-danger">{{ $connection['error'] }}</small>
                @endif
            </div>
        </div>
    </div>

    {{-- Summary --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <div>
                    <small class="text-muted d-block">Total Produk Aktif</small>
                    <span class="fw-bold fs-5">{{ number_format($totalProducts) }}</span>
                </div>
                <div>
                    <small class="text-muted d-block">Match Rate (estimasi)</small>
                    <span class="fw-bold fs-5 text-success">~48.8%</span>
                </div>
                <div>
                    <small class="text-muted d-block">Halaman Ini</small>
                    <span class="fw-bold fs-5">{{ $matchedOnPage }} / {{ $products->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('guest.remote-stock') }}" class="d-flex gap-2 search-box">
                <input type="text" name="q" class="form-control form-control-sm rounded-pill"
                       placeholder="Cari SKU atau nama produk..." value="{{ $search ?? '' }}">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('guest.remote-stock') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Product Stock Table --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-3">#</th>
                            <th>SKU</th>
                            <th>Nama Produk</th>
                            <th>Brand</th>
                            <th class="text-center">Stock Remote</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $i => $product)
                            @php
                                $stockQty = $stockMap[$product->sku] ?? null;
                                $matched = $stockQty !== null;
                            @endphp
                            <tr class="{{ $matched ? 'stock-match' : 'stock-nomatch' }}">
                                <td class="px-3 text-muted">{{ $products->firstItem() + $i }}</td>
                                <td><code>{{ $product->sku }}</code></td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->brand?->brand_name ?? '-' }}</td>
                                <td class="text-center fw-semibold">
                                    @if($matched)
                                        <span class="text-success">{{ number_format($stockQty, 0) }}</span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($matched)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">
                                            <i class="bi bi-check-circle-fill me-1"></i>Match
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                            No Match
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if($search)
                                        Tidak ada produk dengan kata kunci "<strong>{{ $search }}</strong>".
                                    @else
                                        Tidak ada produk.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="d-flex justify-content-between align-items-center px-3 py-3">
                <div class="pagination-info text-muted">
                    Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }}
                    ({{ $products->total() }} total)
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $products->previousPageUrl() }}&q={{ $search ?? '' }}">&laquo;</a>
                        </li>
                        @for($p = max(1, $products->currentPage() - 2); $p <= min($products->lastPage(), $products->currentPage() + 2); $p++)
                            <li class="page-item {{ $p == $products->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $products->url($p) }}&q={{ $search ?? '' }}">{{ $p }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ $products->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $products->nextPageUrl() }}&q={{ $search ?? '' }}">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
