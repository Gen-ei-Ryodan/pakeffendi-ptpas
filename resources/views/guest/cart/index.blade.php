@extends('guest.layouts.app')

@section('title', 'Keranjang Belanja - PAS Market')

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
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

<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4" id="cartItemsHeader">Daftar Belanja ({{ (int) ($summary['total_items'] ?? 0) }} item)</h5>
                        
                        <!-- Cart Items -->
                        <div class="cart-items">
                            @foreach(($cart?->items ?? collect()) as $item)
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
                                        <img src="{{ $imageUrl }}" alt="{{ $product?->name }}" class="img-fluid rounded" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22400%22%3E%3Crect width=%22400%22 height=%22400%22 fill=%22%23f8f9fa%22/%3E%3C/svg%3E'">
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
                            @endforeach
                        </div>
                        
                        <!-- Cart Actions -->
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
                                    <div class="mb-3">
                                        <label for="customer_id" class="form-label fw-bold">Pilih Buyer (Pembayar)</label>
                                        <select name="customer_id" id="customer_id" class="form-select" required>
                                            <option value="" selected disabled>-- Pilih Customer --</option>
                                            @foreach($my_customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->full_name }} {{ $c->company_name ? '('.$c->company_name.')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address_id" class="form-label fw-bold">Pilih Alamat</label>
                                        <select name="address_id" id="address_id" class="form-select" required disabled>
                                            <option value="" selected disabled>-- Pilih customer dulu --</option>
                                        </select>
                                        <div class="form-text" id="salesAddressHelp">Alamat akan muncul setelah buyer dipilih.</div>
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

                                <button type="button" class="btn btn-primary btn-lg w-100 mb-3" @disabled(($summary['total_items'] ?? 0) <= 0 || (!isset($is_sales) || !$is_sales) && ($disableCheckout ?? false)) onclick="confirmCheckout()">
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
                
                <!-- Security Info -->
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
        
        <!-- Empty Cart State (Hidden by default) -->
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

<!-- Checkout Confirmation Modal -->
<div class="modal fade" id="confirmCheckoutModal" tabindex="-1" aria-labelledby="confirmCheckoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="confirmCheckoutModalLabel">Konfirmasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 text-center">
                <p class="mb-0">Apakah Anda yakin ingin melanjutkan ke pembayaran?</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
                <button type="button" class="btn btn-outline-secondary px-4 me-2" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-primary px-4" onclick="submitCheckout()">Ya</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmCheckout() {
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect) {
        if (!customerSelect.value) {
            alert('Silakan pilih customer terlebih dahulu.');
            return;
        }
    }

    const addressSelect = document.querySelector('#checkoutForm select[name="address_id"]');
    if (addressSelect) {
        if (!addressSelect.value) {
            alert('Silakan pilih alamat terlebih dahulu.');
            return;
        }
    }
    
    const modal = new bootstrap.Modal(document.getElementById('confirmCheckoutModal'));
    modal.show();
}

function submitCheckout() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    if (typeof form.requestSubmit === 'function') {
        form.requestSubmit();
        return;
    }

    form.submit();
}

// Cart functionality
document.addEventListener('DOMContentLoaded', function() {
    updateCartSummary();

    const customerSelect = document.getElementById('customer_id');
    const addressSelect = document.getElementById('address_id');
    const addressHelp = document.getElementById('salesAddressHelp');

    if (customerSelect && addressSelect) {
        const resetAddressSelect = (label) => {
            addressSelect.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.disabled = true;
            opt.selected = true;
            opt.textContent = label;
            addressSelect.appendChild(opt);
            addressSelect.disabled = true;
        };

        const setAddresses = (addresses, activeAddressId) => {
            addressSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.disabled = true;
            placeholder.selected = true;
            placeholder.textContent = '-- Pilih Alamat --';
            addressSelect.appendChild(placeholder);

            addresses.forEach((addr) => {
                const opt = document.createElement('option');
                opt.value = String(addr.id);
                const label = (addr.label || 'Alamat') + (addr.is_active ? ' (Aktif)' : '');
                opt.textContent = `${label} - ${addr.full_address}`;
                if (activeAddressId && Number(addr.id) === Number(activeAddressId)) {
                    opt.selected = true;
                    placeholder.selected = false;
                }
                addressSelect.appendChild(opt);
            });

            addressSelect.disabled = addresses.length === 0;
        };

        resetAddressSelect('-- Pilih customer dulu --');

        customerSelect.addEventListener('change', async () => {
            const customerId = customerSelect.value;
            if (!customerId) {
                resetAddressSelect('-- Pilih customer dulu --');
                if (addressHelp) addressHelp.textContent = 'Alamat akan muncul setelah buyer dipilih.';
                return;
            }

            if (addressHelp) addressHelp.textContent = 'Memuat alamat...';
            resetAddressSelect('Memuat alamat...');

            try {
                const res = await fetch(`/cart/customers/${customerId}/addresses`, {
                    headers: { 'Accept': 'application/json' }
                });

                if (!res.ok) {
                    resetAddressSelect('Gagal memuat alamat');
                    if (addressHelp) addressHelp.textContent = 'Gagal memuat alamat. Coba refresh halaman.';
                    return;
                }

                const data = await res.json();
                const addresses = Array.isArray(data.addresses) ? data.addresses : [];
                const activeAddressId = data.active_address_id || null;

                if (addresses.length === 0) {
                    resetAddressSelect('Customer belum punya alamat');
                    if (addressHelp) addressHelp.textContent = 'Customer belum punya alamat. Tambahkan alamat dulu sebelum checkout.';
                    return;
                }

                setAddresses(addresses, activeAddressId);
                if (addressHelp) addressHelp.textContent = 'Pilih alamat pengiriman untuk order ini.';
            } catch (e) {
                resetAddressSelect('Gagal memuat alamat');
                if (addressHelp) addressHelp.textContent = 'Gagal memuat alamat. Coba refresh halaman.';
            }
        });
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
</script>
@endpush
