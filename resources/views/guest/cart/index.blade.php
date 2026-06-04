@extends('guest.layouts.app')

@section('title', 'Keranjang Belanja - PAS Market')

@section('mobile-topbar-inner')
<div class="cart-mob-header">
    <span class="cart-mob-title">Keranjang</span>
    <div class="cart-mob-actions">
        <a href="{{ url('/products') }}" class="cart-mob-icon"><i class="bi bi-search"></i></a>
        <a href="{{ url('/cart') }}" class="cart-mob-icon"><i class="bi bi-cart3"></i></a>
    </div>
</div>
@endsection

@section('content')
<!-- Desktop: Page Header (hidden on mobile) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-secondary mt-3 mb-0">Keranjang Belanja</h1>
    </div>
</section>

<!-- ====================== DESKTOP CART LAYOUT ====================== -->
<section class="py-5 mobile-hide">
    <div class="container">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                {{-- Sales Customer Selector --}}
                @if(isset($is_sales) && $is_sales)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body py-3">
                        @if($my_customers->isEmpty())
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold text-nowrap"><i class="bi bi-person-badge me-1"></i>Keranjang Untuk:</span>
                                <span class="text-muted small">Belum ada customer. Silakan tambahkan melalui menu Profile.</span>
                            </div>
                        @else
                        <div class="d-flex align-items-center gap-3" id="custBar">
                            <span class="fw-bold text-nowrap"><i class="bi bi-person-badge me-1"></i>Keranjang Untuk:</span>
                            {{-- Badge + Ganti Button --}}
                            <div id="custBadgeRow" class="d-flex align-items-center gap-3 w-100 @if(!$selected_customer) d-none @endif">
                                <span class="badge bg-primary fs-6 px-3 py-2">{{ $selected_customer?->full_name }}</span>
                                <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" onclick="showCustomerSelect()">Ganti Customer</button>
                            </div>
                            {{-- Dropdown + Pilih + Batal --}}
                            <div id="custSelectRow" class="d-flex align-items-center gap-2 w-100 @if($selected_customer) d-none @endif">
                                <select id="customerSelect" class="form-select form-select-sm" style="max-width: 300px;">
                                    <option value="" selected disabled>-- Pilih Customer --</option>
                                    @foreach($my_customers as $c)
                                        <option value="{{ route('guest.cart.select-customer', $c->id) }}">{{ $c->full_name }} {{ $c->company_name ? '('.$c->company_name.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="goToCustomer()">Pilih</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap" onclick="cancelCustomerSelect()">Batal</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" id="cartItemsHeader">Daftar Belanja ({{ (int) ($summary['total_items'] ?? 0) }} item)</h5>
                        
                        <div class="cart-items">
                            @forelse(($cart?->items ?? collect()) as $item)
                            @php
                                $product = $item->product;
                                $imageUrl = $product?->photo_url ?? asset('guest/img/placeholder-product.svg');
                                $qty = (int) $item->quantity;
                                $pricing = $product ? $product->pricingForQuantity($qty) : null;
                                $unitPrice = (float) ($pricing['unit_price'] ?? 0);
                                $discountPercent = (float) ($pricing['discount_percent'] ?? 0);
                                $netPrice = (float) ($pricing['net_price'] ?? 0);
                                $lineTotal = $netPrice * $qty;
                            @endphp
                            <div class="cart-item border-bottom pb-3 mb-3" data-product-id="{{ $product?->id }}">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="{{ $imageUrl }}" alt="{{ $product?->name }}" class="img-fluid rounded" onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="fw-bold mb-1">{{ $product?->name }}</h6>
                                        @if(($product?->variant ?? '') !== '')
                                            <p class="text-muted small mb-1">{{ $product?->variant }}</p>
                                        @endif
                                        <p class="text-muted small mb-1">Brand: {{ $product?->brand?->brand_name ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group input-group-sm" style="min-width: 120px;">
                                            <button class="btn btn-secondary btn-sm flex-shrink-0" type="button" onclick="updateQuantity({{ $product?->id }}, -1)">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="number" class="form-control text-center" 
                                                   value="{{ (int) $item->quantity }}" min="1" max="9999"
                                                   onchange="updateQuantityDirect({{ $product?->id }}, this.value)">
                                            <button class="btn btn-secondary btn-sm flex-shrink-0" type="button" onclick="updateQuantity({{ $product?->id }}, 1)">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <div class="fw-bold text-primary" data-line-total>Rp {{ number_format($lineTotal, 0, ',', '.') }}</div>
                                        <small class="text-muted" data-unit-price>
                                            Rp {{ number_format($netPrice, 0, ',', '.') }}/pcs
                                            @if($discountPercent > 0)
                                                <span class="text-muted"> (disc {{ rtrim(rtrim(number_format($discountPercent, 2, '.', ''), '0'), '.') }}%)</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button class="btn btn-outline-danger btn-sm" onclick="removeItem({{ $product?->id }})" data-bs-toggle="tooltip" title="Hapus item">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                                @if(isset($is_sales) && $is_sales && !$selected_customer)
                                    <h5 class="text-muted mb-2">Pilih Customer Terlebih Dahulu</h5>
                                    <p class="text-muted small mb-0">Silakan pilih customer di atas untuk melihat keranjang belanja.</p>
                                @else
                                    <h5 class="text-muted mb-2">Keranjang Kosong</h5>
                                    <p class="text-muted small mb-0">Belum ada produk di keranjang. Yuk, mulai belanja!</p>
                                @endif
                            </div>
                            @endforelse
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-3">
                            <button class="btn btn-outline-secondary" onclick="clearCart()">
                                <i class="bi bi-trash me-2"></i>Kosongkan Keranjang
                            </button>
                            <a href="{{ url('/products') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-4">Ringkasan Pesanan</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal ({{ (int) ($summary['total_items'] ?? 0) }} item)</span>
                            <span id="cartSubtotal">Rp {{ number_format((float) ($summary['subtotal'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold h5 text-primary" id="cartGrandTotal">Rp {{ number_format((float) ($summary['grand_total'] ?? 0), 0, ',', '.') }}</span>
                        </div>
                        @if($customer)
                            <form method="POST" action="{{ route('guest.cart.checkout') }}" id="checkoutForm" data-ajax="false">
                                @csrf

                                @if(isset($is_sales) && $is_sales)
                                    @if(!$selected_customer)
                                        <div class="alert alert-warning mb-3">
                                            <i class="bi bi-exclamation-triangle me-2"></i>Silakan pilih customer di atas sebelum checkout.
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Buyer</label>
                                            <div class="border rounded p-2 bg-light">
                                                <span class="fw-semibold">{{ $selected_customer->full_name }}</span>
                                                @if($selected_customer->company_name)
                                                    <span class="text-muted">({{ $selected_customer->company_name }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="address_id" class="form-label fw-bold">Pilih Alamat</label>
                                        @php
                                            $salesAddrList = $addresses ?? collect();
                                            $salesActiveAddrId = $active_address_id ?? null;
                                        @endphp
                                        @if($salesAddrList->isEmpty())
                                            <div class="alert alert-warning mb-2">Customer belum memiliki alamat.</div>
                                        @elseif($salesAddrList->count() === 1)
                                            <input type="hidden" name="address_id" value="{{ $salesAddrList->first()->id }}">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="fw-semibold">{{ $salesAddrList->first()->label ?: 'Alamat' }} <span class="badge bg-success ms-2">Aktif</span></div>
                                                <div class="small text-muted">{{ $salesAddrList->first()->recipient_name ?: $selected_customer->full_name }}{{ $salesAddrList->first()->phone ? ' · '.$salesAddrList->first()->phone : '' }}</div>
                                                <div class="mt-1">{{ $salesAddrList->first()->full_address }}</div>
                                            </div>
                                        @else
                                            <select name="address_id" id="address_id" class="form-select" required>
                                                <option value="" disabled {{ !$salesActiveAddrId ? 'selected' : '' }}>-- Pilih Alamat --</option>
                                                @foreach($salesAddrList as $addr)
                                                    <option value="{{ $addr->id }}" {{ (int) $addr->id === (int) $salesActiveAddrId ? 'selected' : '' }}>
                                                        {{ $addr->label ?: 'Alamat' }}{{ $addr->is_active ? ' (Aktif)' : '' }} - {{ $addr->full_address }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                @else
                                    @php
                                        $addresses = $addresses ?? collect();
                                        $activeAddressId = $active_address_id ?? null;
                                        $hasAddresses = $addresses->count() > 0;
                                        $disableCheckout = ! $hasAddresses;
                                    @endphp

                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Pilih Alamat</label>
                                        @if(! $hasAddresses)
                                            <div class="alert alert-warning mb-2">Silakan tambah alamat dulu sebelum checkout.</div>
                                            <a href="{{ url('/profile/addresses') }}" class="btn btn-outline-primary btn-sm w-100">Tambah Alamat</a>
                                        @elseif($addresses->count() === 1)
                                            <input type="hidden" name="address_id" value="{{ $addresses->first()->id }}">
                                            <div class="border rounded p-2 bg-light">
                                                <div class="fw-semibold">{{ $addresses->first()->label ?: 'Alamat' }} <span class="badge bg-success ms-2">Aktif</span></div>
                                                <div class="small text-muted">{{ $addresses->first()->recipient_name ?: $customer->full_name }}{{ $addresses->first()->phone ? ' · '.$addresses->first()->phone : '' }}</div>
                                                <div class="mt-1">{{ $addresses->first()->full_address }}</div>
                                            </div>
                                        @else
                                            <select name="address_id" class="form-select" required>
                                                <option value="" disabled {{ !$activeAddressId ? 'selected' : '' }}>-- Pilih Alamat --</option>
                                                @foreach($addresses as $addr)
                                                    <option value="{{ $addr->id }}" {{ (int) $addr->id === (int) $activeAddressId ? 'selected' : '' }}>
                                                        {{ $addr->label ?: 'Alamat' }}{{ $addr->is_active ? ' (Aktif)' : '' }} - {{ $addr->full_address }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="mt-2">
                                                <a href="{{ url('/profile/addresses') }}" class="btn btn-outline-secondary btn-sm w-100">Kelola Alamat</a>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @php
                                    $noItems = ($summary['total_items'] ?? 0) <= 0;
                                    $salesNoCustomer = isset($is_sales) && $is_sales && !$selected_customer;
                                    $customerNoAddress = (!isset($is_sales) || !$is_sales) && ($disableCheckout ?? false);
                                    $disableBtn = $noItems || $salesNoCustomer || $customerNoAddress;
                                @endphp
                                <button type="button" class="btn btn-primary btn-lg w-100 mb-3" @disabled($disableBtn) onclick="confirmCheckout()">
                                    <i class="bi bi-credit-card me-2"></i>Lanjut ke Pembayaran
                                </button>
                            </form>
                        @else
                            <a class="btn btn-primary btn-lg w-100 mb-3" href="{{ url('/login') }}">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Checkout
                            </a>
                        @endif
                        
                        <div class="text-center">
                            <a href="{{ url('/products') }}" class="text-decoration-none">
                                <i class="bi bi-plus-circle me-1"></i>Tambah produk lain
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-3">Keamanan Transaksi</h6>
                        <div class="row g-3">
                            <div class="col-4 text-center">
                                <i class="bi bi-shield-check text-primary fs-4"></i>
                                <div class="small text-muted">Aman</div>
                            </div>
                            <div class="col-4 text-center">
                                <i class="bi bi-lock text-primary fs-4"></i>
                                <div class="small text-muted">Terenkripsi</div>
                            </div>
                            <div class="col-4 text-center">
                                <i class="bi bi-check-circle text-primary fs-4"></i>
                                <div class="small text-muted">Terverifikasi</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Empty Cart State -->
        <div class="row d-none" id="emptyCart">
            <div class="col-12 text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted mb-4"></i>
                <h4 class="text-muted mb-3">Keranjang Belanja Kosong</h4>
                <p class="text-muted mb-4">Yuk, mulai belanja dan temukan produk favoritmu!</p>
                <a href="{{ url('/products') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-shop me-2"></i>Mulai Belanja
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE CART LAYOUT ====================== -->
<section class="d-lg-none" id="mobCartSection">
    {{-- Sales Customer Selector (Mobile) --}}
    @if(isset($is_sales) && $is_sales)
    <div class="mob-sales-customer-bar">
        <div class="d-flex align-items-center gap-2 px-3 py-2" style="background:#fff; border-bottom:1px solid #e5e7eb;">
            <span class="fw-bold small text-nowrap"><i class="bi bi-person-badge me-1"></i>Keranjang:</span>
            @if($my_customers->isEmpty())
                <span class="text-muted small">Belum ada customer.</span>
            @else
            {{-- Badge + Ganti (mobile) --}}
            <div id="mobCustBadgeRow" class="d-flex align-items-center gap-2 w-100 @if(!$selected_customer) d-none @endif">
                <span class="badge bg-primary">{{ $selected_customer?->full_name }}</span>
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 ms-auto" style="font-size:0.75rem;" onclick="showMobCustomerSelect()">Ganti</button>
            </div>
            {{-- Dropdown + Pilih + Batal (mobile) --}}
            <div id="mobCustSelectRow" class="d-flex align-items-center gap-2 w-100 @if($selected_customer) d-none @endif">
                <select id="mobCustomerSelect" class="form-select form-select-sm">
                    <option value="" selected disabled>-- Pilih Customer --</option>
                    @foreach($my_customers as $c)
                        <option value="{{ route('guest.cart.select-customer', $c->id) }}">{{ $c->full_name }}</option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-primary btn-sm text-nowrap" onclick="goToMobCustomer()">Pilih</button>
                <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 text-nowrap" style="font-size:0.75rem;" onclick="cancelMobCustomerSelect()">Batal</button>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="mob-cart-list" id="mobCartList">
        @forelse(($cart?->items ?? collect()) as $item)
        @php
            $product = $item->product;
            $imageUrl = $product?->photo_url ?? asset('guest/img/placeholder-product.svg');
            $qty = (int) $item->quantity;
            $pricing = $product ? $product->pricingForQuantity($qty) : null;
            $netPrice = (float) ($pricing['net_price'] ?? 0);
            $lineTotal = $netPrice * $qty;
        @endphp
        <div class="mob-cart-item" data-product-id="{{ $product?->id }}" data-item-id="{{ $item->id }}">
            <div class="mob-cart-left">
                <button class="mob-cart-check" data-check-item>
                    <i class="bi bi-check-lg"></i>
                </button>
            </div>
            <div class="mob-cart-img">
                <img src="{{ $imageUrl }}" alt="{{ $product?->name }}"
                     onerror="this.onerror=null;this.src='{{ asset('guest/img/placeholder-product.svg') }}'">
            </div>
            <div class="mob-cart-body">
                <div class="mob-cart-brand">{{ $product?->brand?->brand_name ?? 'Produk' }}</div>
                <div class="mob-cart-name">{{ $product?->name }}</div>
                @if(($product?->variant ?? '') !== '')
                    <div class="mob-cart-variant">{{ $product?->variant }}</div>
                @endif
                <div class="mob-cart-price" data-line-total>Rp {{ number_format($lineTotal, 0, ',', '.') }}</div>
                <div class="mob-cart-actions">
                    <div class="mob-qty-wrap">
                        <button class="mob-qty-btn" onclick="mobUpdateQty({{ $product?->id }}, -1)"><i class="bi bi-dash"></i></button>
                        <input type="number" class="mob-qty-input" value="{{ $qty }}" min="1" readonly>
                        <button class="mob-qty-btn" onclick="mobUpdateQty({{ $product?->id }}, 1)"><i class="bi bi-plus"></i></button>
                    </div>
                    <button class="mob-cart-del" onclick="mobRemoveItem({{ $product?->id }})"><i class="bi bi-trash3"></i></button>
                </div>
            </div>
        </div>
        @empty
        <div class="mob-cart-empty">
            <i class="bi bi-cart-x"></i>
            <p>Keranjang masih kosong</p>
            <a href="{{ url('/products') }}" class="mob-cart-shop-btn">Mulai Belanja</a>
        </div>
        @endforelse
    </div>
</section>

<!-- ====================== MOBILE STICKY BOTTOM BAR ====================== -->
@if(($cart?->items ?? collect())->count() > 0)
<div class="mob-checkout-bar d-lg-none" id="mobCheckoutBar"
     data-has-address="{{ (int) (($addresses ?? collect())->count() > 0) }}"
     data-is-logged-in="{{ (int) ($customer ? true : false) }}">
    <div class="mob-checkout-left">
        <button class="mob-check-all" id="mobCheckAll">
            <i class="bi bi-check-lg"></i>
        </button>
        <span class="mob-check-all-label">Semua</span>
    </div>
    <div class="mob-checkout-mid">
        <span class="mob-total-label">Total</span>
        <span class="mob-total-price" id="mobTotalPrice">Rp {{ number_format((float) ($summary['grand_total'] ?? 0), 0, ',', '.') }}</span>
    </div>
    <button class="mob-checkout-btn" id="mobCheckoutBtn">Checkout</button>
</div>
@endif

<!-- Mobile: Alamat Prompt -->
<div class="modal fade d-lg-none" id="mobAddressPrompt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-body text-center py-4">
                <i class="bi bi-geo-alt-fill" style="font-size: 2.5rem; color: var(--primary-color);"></i>
                <h6 class="fw-bold mt-2 mb-1">Alamat Belum Diisi</h6>
                <p class="text-muted small mb-3">Silakan tambah alamat pengiriman dulu sebelum checkout.</p>
                <a href="{{ url('/profile/addresses') }}" class="btn btn-primary w-100 mb-2">Tambah Alamat</a>
                <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal" style="border:1px solid #e5e7eb;">Nanti Saja</button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile: Error Messages -->
@if($errors->any())
<div class="d-lg-none mob-cart-errors">
    @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
    @endforeach
</div>
@endif

<!-- Mobile: Hidden Checkout Form -->
@php
    $mobAddrId = $active_address_id ?? (($addresses ?? collect())->first()?->id ?? '');
@endphp
<form method="POST" action="{{ route('guest.cart.checkout') }}" id="mobCheckoutForm" style="display:none;">
    @csrf
    @if(isset($is_sales) && $is_sales)
        <input type="hidden" name="address_id" id="mobSalesAddressId" value="">
    @else
        @if($customer && $mobAddrId)
            <input type="hidden" name="address_id" value="{{ $mobAddrId }}">
        @endif
    @endif
</form>

<!-- Confirm Checkout Modal -->
<div class="modal fade" id="confirmCheckoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Konfirmasi Checkout</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin melanjutkan ke pembayaran?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border: 1px solid #e5e7eb;">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitCheckout()">Lanjutkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showCustomerSelect() {
    var badge = document.getElementById('custBadgeRow');
    var select = document.getElementById('custSelectRow');
    if (badge) badge.classList.add('d-none');
    if (select) select.classList.remove('d-none');
}

function cancelCustomerSelect() {
    var badge = document.getElementById('custBadgeRow');
    var select = document.getElementById('custSelectRow');
    if (badge) badge.classList.remove('d-none');
    if (select) select.classList.add('d-none');
}

function showMobCustomerSelect() {
    var badge = document.getElementById('mobCustBadgeRow');
    var select = document.getElementById('mobCustSelectRow');
    if (badge) badge.classList.add('d-none');
    if (select) select.classList.remove('d-none');
}

function cancelMobCustomerSelect() {
    var badge = document.getElementById('mobCustBadgeRow');
    var select = document.getElementById('mobCustSelectRow');
    if (badge) badge.classList.remove('d-none');
    if (select) select.classList.add('d-none');
}

function goToCustomer() {
    var sel = document.getElementById('customerSelect');
    if (sel && sel.value) {
        window.location.href = sel.value;
    }
}

function goToMobCustomer() {
    var sel = document.getElementById('mobCustomerSelect');
    if (sel && sel.value) {
        window.location.href = sel.value;
    }
}

function confirmCheckout() {
    var isSales = {{ isset($is_sales) && $is_sales ? 'true' : 'false' }};
    if (isSales) {
        var selectedCustomer = {{ $selected_customer ? 'true' : 'false' }};
        if (!selectedCustomer) {
            alert('Silakan pilih customer terlebih dahulu.');
            return;
        }
    }

    var addressSelect = document.querySelector('#checkoutForm select[name="address_id"]');
    if (addressSelect) {
        if (!addressSelect.value) {
            alert('Silakan pilih alamat terlebih dahulu.');
            return;
        }
    }

    var form = document.getElementById('checkoutForm') || document.getElementById('mobCheckoutForm');
    if (!form) return;

    // For sales on mobile: sync address_id
    if (isSales) {
        var deskAddr = document.querySelector('#checkoutForm select[name="address_id"]');
        var mobAddr = document.getElementById('mobSalesAddressId');
        if (deskAddr && mobAddr) mobAddr.value = deskAddr.value;
    }

    var modalEl = document.getElementById('confirmCheckoutModal');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

function submitCheckout() {
    var form = document.getElementById('checkoutForm');
    if (!form) {
        form = document.getElementById('mobCheckoutForm');
    }
    if (!form) return;

    // For sales on mobile: sync address_id
    var isSales = {{ isset($is_sales) && $is_sales ? 'true' : 'false' }};
    if (isSales) {
        var deskAddr = document.querySelector('#checkoutForm select[name="address_id"]');
        var mobAddr = document.getElementById('mobSalesAddressId');
        if (deskAddr && mobAddr) mobAddr.value = deskAddr.value;
    }

    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
        return;
    }
    form.submit();
}

// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    updateCartSummary();

    const addressSelect = document.getElementById('address_id');
    if (addressSelect && !addressSelect.value) {
        // Auto-select first option if available
        var firstOpt = addressSelect.querySelector('option:not([disabled])');
        if (firstOpt) firstOpt.selected = true;
    }

    // Initialize mobile sales address ID
    var isSales = {{ isset($is_sales) && $is_sales ? 'true' : 'false' }};
    if (isSales) {
        var deskAddr = document.querySelector('#checkoutForm select[name="address_id"], #checkoutForm input[name="address_id"]');
        var mobAddr = document.getElementById('mobSalesAddressId');
        if (deskAddr && mobAddr) {
            mobAddr.value = deskAddr.value;
        }

        // Update mobile address ID when desktop address changes
        if (addressSelect) {
            addressSelect.addEventListener('change', function() {
                if (mobAddr) {
                    mobAddr.value = this.value;
                }
            });
        }
    }
});

function updateQuantity(productId, change) {
    const input = document.querySelector(`[data-product-id="${productId}"] input[type="number"]`);
    if (input) {
        let currentValue = parseInt(input.value) || 1;
        let newValue = currentValue + change;
        let maxValue = parseInt(input.max) || 9999;
        
        if (newValue >= 1 && newValue <= maxValue) {
            input.value = newValue;
            updateCartItem(productId, newValue);
        }
    }
}

function updateQuantityDirect(productId, newValue) {
    let value = parseInt(newValue) || 1;
    let maxValue = 9999;
    
    if (value < 1) value = 1;
    if (value > maxValue) value = maxValue;
    
    const input = document.querySelector(`[data-product-id="${productId}"] input[type="number"]`);
    if (input) {
        input.value = value;
    }
    
    updateCartItem(productId, value);
}

function csrfToken() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

async function updateCartItem(productId, quantity) {
    const res = await fetch(`/cart/items/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ quantity })
    });

    if (!res.ok) {
        PAS.Cart.showNotification('Gagal memperbarui keranjang', 'danger');
        return;
    }

    updateCartSummary();
    PAS.Cart.showNotification('Keranjang diperbarui', 'success');
}

async function removeItem(productId) {
    if (!confirm('Yakin ingin menghapus item ini dari keranjang?')) return;

    const res = await fetch(`/cart/items/${productId}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        }
    });

    if (!res.ok) {
        PAS.Cart.showNotification('Gagal menghapus item', 'danger');
        return;
    }

    const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
    if (itemElement) {
        itemElement.remove();
    }

    updateCartSummary();
    PAS.Cart.showNotification('Item dihapus dari keranjang', 'info');
}

async function clearCart() {
    if (!confirm('Yakin ingin mengosongkan keranjang belanja?')) return;

    const res = await fetch(`/cart`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        }
    });

    if (!res.ok) {
        PAS.Cart.showNotification('Gagal mengosongkan keranjang', 'danger');
        return;
    }

    const container = document.querySelector('.cart-items');
    if (container) container.innerHTML = '';
    updateCartSummary();
    PAS.Cart.showNotification('Keranjang dikosongkan', 'info');
}

async function updateCartSummary() {
    const items = document.querySelectorAll('.cart-item');
    const cartItemsDiv = document.querySelector('.cart-items');
    const emptyCartDiv = document.getElementById('emptyCart');
    
    if (items.length === 0) {
        // Show empty cart state
        if (cartItemsDiv) cartItemsDiv.style.display = 'none';
        if (emptyCartDiv) emptyCartDiv.classList.remove('d-none');
        
        // Hide cart sections
        document.querySelector('.col-lg-8').style.display = 'none';
        document.querySelector('.col-lg-4').style.display = 'none';
        return;
    } else {
        if (cartItemsDiv) cartItemsDiv.style.display = '';
        if (emptyCartDiv) emptyCartDiv.classList.add('d-none');
        document.querySelector('.col-lg-8').style.display = '';
        document.querySelector('.col-lg-4').style.display = '';
    }

    const res = await fetch('/cart/summary', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        },
    });

    if (!res.ok) {
        return;
    }

    const data = await res.json();
    const summary = data?.summary || {};
    const totalItems = parseInt(summary.total_items || 0);

    const header = document.getElementById('cartItemsHeader');
    if (header) {
        header.innerHTML = `Daftar Belanja (${totalItems} item)`;
    }

    const subtotalEl = document.getElementById('cartSubtotal');
    const grandTotalEl = document.getElementById('cartGrandTotal');
    if (subtotalEl) subtotalEl.textContent = `Rp ${Math.round(Number(summary.subtotal || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`;
    if (grandTotalEl) grandTotalEl.textContent = `Rp ${Math.round(Number(summary.grand_total || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`;

    const byProductId = new Map();
    (summary.items || []).forEach((it) => {
        byProductId.set(String(it.product_id), it);
    });

    document.querySelectorAll('.cart-item[data-product-id]').forEach((row) => {
        const productId = row.getAttribute('data-product-id');
        const it = byProductId.get(String(productId));
        if (!it) return;

        const lineTotalEl = row.querySelector('[data-line-total]');
        if (lineTotalEl) {
            lineTotalEl.textContent = `Rp ${Math.round(Number(it.line_total || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.')}`;
        }

        const unitPriceEl = row.querySelector('[data-unit-price]');
        if (unitPriceEl) {
            const net = Math.round(Number(it.net_price || 0)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            const disc = Number(it.discount_percent || 0);
            const discText = disc > 0 ? ` (disc ${String(disc).replace(/\.0+$/, '')}%)` : '';
            unitPriceEl.textContent = `Rp ${net}/pcs${discText}`;
        }
    });
}

// ==================== MOBILE CART FUNCTIONS ====================

function mobUpdateQty(productId, change) {
    const item = document.querySelector(`.mob-cart-item[data-product-id="${productId}"]`);
    if (!item) return;
    const input = item.querySelector('.mob-qty-input');
    let val = (parseInt(input.value) || 1) + change;
    if (val < 1) val = 1;

    input.value = val;

    fetch('/cart/items/' + productId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        },
        body: JSON.stringify({ quantity: val })
    })
    .then(function(res) {
        if (!res.ok) throw new Error();
        updateCartSummary();
        PAS.Cart.showNotification('Keranjang diperbarui', 'success');
    })
    .catch(function() {
        PAS.Cart.showNotification('Gagal memperbarui keranjang', 'danger');
    });
}

function mobRemoveItem(productId) {
    if (!confirm('Yakin ingin menghapus item ini dari keranjang?')) return;

    const item = document.querySelector(`.mob-cart-item[data-product-id="${productId}"]`);

    fetch('/cart/items/' + productId, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken()
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (item) item.remove();
        updateCartSummary();
        PAS.Cart.showNotification('Item dihapus dari keranjang', 'info');
        mobCheckEmpty();
    })
    .catch(function() {
        PAS.Cart.showNotification('Gagal menghapus item', 'danger');
    });
}

function updateMobCartSummary(summary) {
    const totalEl = document.getElementById('mobTotalPrice');
    if (totalEl && summary.grand_total) {
        totalEl.textContent = 'Rp ' + Math.round(Number(summary.grand_total)).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
}

function mobCheckEmpty() {
    const items = document.querySelectorAll('.mob-cart-item');
    const bar = document.getElementById('mobCheckoutBar');
    const list = document.getElementById('mobCartList');
    const emptyMsg = document.querySelector('.mob-cart-empty');

    if (items.length === 0 && list) {
        if (!emptyMsg) {
            var msg = document.createElement('div');
            msg.className = 'mob-cart-empty';
            msg.innerHTML = '<i class="bi bi-cart-x"></i><p>Keranjang masih kosong</p><a href="{{ url('/products') }}" class="mob-cart-shop-btn">Mulai Belanja</a>';
            list.appendChild(msg);
        }
        if (bar) bar.style.display = 'none';
    } else {
        if (bar) bar.style.display = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-check all items on mobile
    document.querySelectorAll('.mob-cart-check').forEach(function(btn) {
        btn.classList.add('checked');
    });
    mobUpdateCheckoutState();

    // Mobile checkbox toggle
    document.querySelectorAll('.mob-cart-check').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.classList.toggle('checked');
            mobUpdateCheckoutState();
        });
    });

    // Select all
    var checkAllBtn = document.getElementById('mobCheckAll');
    if (checkAllBtn) {
        checkAllBtn.addEventListener('click', function() {
            var isAll = this.classList.toggle('checked');
            document.querySelectorAll('.mob-cart-check').forEach(function(btn) {
                btn.classList.toggle('checked', isAll);
            });
            mobUpdateCheckoutState();
        });
    }

    // Checkout button
    var checkoutBtn = document.getElementById('mobCheckoutBtn');
    var checkoutBar = document.getElementById('mobCheckoutBar');
    var isSales = {{ isset($is_sales) && $is_sales ? 'true' : 'false' }};
    
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            // Check login
            var isLoggedIn = checkoutBar ? parseInt(checkoutBar.dataset.isLoggedIn) : 0;
            if (!isLoggedIn) {
                window.location.href = '{{ url('/login') }}?redirect={{ url('/cart') }}';
                return;
            }

            if (isSales) {
                // For sales: check customer and address
                var selectedCustomer = {{ $selected_customer ? 'true' : 'false' }};
                if (!selectedCustomer) {
                    alert('Silakan pilih customer terlebih dahulu.');
                    return;
                }

                // Sync address_id from desktop to mobile
                var deskAddr = document.querySelector('#checkoutForm select[name="address_id"]');
                var mobAddr = document.getElementById('mobSalesAddressId');
                if (deskAddr && mobAddr) {
                    mobAddr.value = deskAddr.value;
                }

                // Check if address_id is set
                if (!mobAddr || !mobAddr.value) {
                    alert('Silakan pilih alamat terlebih dahulu.');
                    return;
                }
            } else {
                // For regular customers: check address
                var hasAddress = checkoutBar ? parseInt(checkoutBar.dataset.hasAddress) : 0;
                if (!hasAddress) {
                    var addrModalEl = document.getElementById('mobAddressPrompt');
                    if (addrModalEl) {
                        var addrModal = new bootstrap.Modal(addrModalEl);
                        addrModal.show();
                    }
                    return;
                }
            }

            // Direct submit (skip confirmation modal on mobile)
            var form = document.getElementById('mobCheckoutForm');
            if (form) {
                form.submit();
            }
        });
    }
});

function mobUpdateCheckoutState() {
    var checked = document.querySelectorAll('.mob-cart-check.checked');
    var all = document.querySelectorAll('.mob-cart-check');
    var checkAllBtn = document.getElementById('mobCheckAll');
    var totalPrice = 0;

    // Determine if all checked
    if (checkAllBtn) {
        checkAllBtn.classList.toggle('checked', checked.length === all.length && all.length > 0);
    }

    // Calculate total price from checked items
    checked.forEach(function(btn) {
        var item = btn.closest('.mob-cart-item');
        if (item) {
            var priceEl = item.querySelector('.mob-cart-price');
            if (priceEl) {
                var raw = priceEl.textContent.replace(/[^0-9]/g, '');
                totalPrice += parseInt(raw) || 0;
            }
        }
    });

    var totalEl = document.getElementById('mobTotalPrice');
    if (totalEl) {
        totalEl.textContent = 'Rp ' + totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    var checkoutBtn = document.getElementById('mobCheckoutBtn');
    if (checkoutBtn) {
        checkoutBtn.style.opacity = checked.length > 0 ? '1' : '0.4';
    }
}
</script>
@endpush
