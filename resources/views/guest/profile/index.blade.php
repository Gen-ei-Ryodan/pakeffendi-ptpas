@extends('guest.layouts.app')

@section('title', 'Profil Saya - PAS Market')

@section('content')
<!-- Page Header (Desktop) -->
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil</li>
            </ol>
        </nav>
        <h1 class="h3 fw-bold text-secondary mt-3 mb-0">Profil Saya</h1>
    </div>
</section>

<!-- Profile Content (Desktop) -->
<section class="py-5 mobile-hide">
    <div class="container">
        <div class="row">
            @include('guest.partials.profile-sidebar')

            <div class="col-lg-9">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="fw-bold mb-0">Informasi Pribadi</h5>
                    </div>
                    <div class="card-body">
                        @if(session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('guest.profile.update') }}" novalidate>
                            @csrf
                            @if(isset($is_sales) && $is_sales)
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email) }}" required>
                                </div>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>Akun Sales. Informasi detail lainnya dikelola oleh Admin.
                                </div>
                            @else
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="fullName" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="fullName" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{ $customer->email }}" disabled>
                                    </div>
                                </div>
                                <div class="row g-3 mt-0">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Nomor HP</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" required>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                @if(isset($is_sales) && $is_sales)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">Riwayat Order</h5>
                            <a href="{{ route('guest.orders.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-box-arrow-right me-1"></i>Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="get" action="{{ route('guest.profile.index') }}" class="row g-2 align-items-end mb-3" data-ajax="false">
                            <div class="col-12 col-md-4">
                                <label class="form-label mb-1">Nama Customer</label>
                                <input type="text" name="customer" value="{{ $order_filters['customer'] ?? '' }}" class="form-control form-control-sm" placeholder="Contoh: Budi">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1">Dari</label>
                                <input type="date" name="date_from" value="{{ $order_filters['date_from'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label mb-1">Sampai</label>
                                <input type="date" name="date_to" value="{{ $order_filters['date_to'] ?? '' }}" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1">Search</label>
                                <input type="text" name="q" value="{{ $order_filters['q'] ?? '' }}" class="form-control form-control-sm" placeholder="No order / barang">
                            </div>
                            <div class="col-6 col-md-1 d-grid">
                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i></button>
                            </div>
                            <div class="col-6 col-md-1 d-grid">
                                <a href="{{ route('guest.profile.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </a>
                            </div>
                        </form>
                        <div class="row g-2 mb-3">
                            <div class="col-12 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Periode</div>
                                    <div class="fw-semibold">{{ ($order_filters['date_from'] ?? '-') }} s/d {{ ($order_filters['date_to'] ?? '-') }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Total Nominal</div>
                                    <div class="fw-semibold">Rp {{ number_format((float) ($order_stats['total_nominal'] ?? 0), 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 bg-light">
                                    <div class="text-muted small">Total Transaksi</div>
                                    <div class="fw-semibold">{{ (int) ($order_stats['total_transaksi'] ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th>No. Pesanan</th><th>Nama Customer</th><th>Tanggal</th><th>Status</th><th>Total</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    @forelse(($orders ?? collect()) as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_no }}</td>
                                        <td>{{ $order->customer?->full_name ?? '-' }}</td>
                                        <td>{{ $order->order_date?->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                        <td class="fw-semibold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                        <td><a href="{{ route('guest.orders.show', $order) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="6" class="text-muted text-center py-4">Tidak ada order.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(isset($orders) && method_exists($orders, 'links'))
                            <div class="mt-3 d-flex justify-content-center">{{ $orders->links() }}</div>
                        @endif
                    </div>
                </div>
                @else
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Pesanan Terbaru</h5>
                        <a href="{{ route('guest.orders.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-box-arrow-right me-1"></i>Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead><tr><th>No. Pesanan</th><th>Tanggal</th><th>Status</th><th>Total</th><th>Aksi</th></tr></thead>
                                <tbody>
                                    @forelse(($recentOrders ?? collect()) as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_no }}</td>
                                        <td>{{ $order->order_date?->format('Y-m-d') }}</td>
                                        <td><span class="badge bg-secondary">{{ $order->status }}</span></td>
                                        <td class="fw-semibold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                        <td><a href="{{ route('guest.orders.show', $order) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a></td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-muted text-center py-4">Belum ada pesanan.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE PROFILE ====================== -->
<section class="d-lg-none" id="mobProfileSection">
    @php
        $profileName = isset($is_sales) && $is_sales ? ($customer->name ?? 'User') : ($customer->full_name ?? 'User');
        $profileEmail = $customer->email ?? '';
        $profilePhone = $customer->phone ?? '';
        $initials = strtoupper(substr($profileName, 0, 1));
    @endphp

    <!-- Compact Profile Header -->
    <div class="mob-prof-header">
        <div class="mob-prof-avatar">{{ $initials }}</div>
        <div class="mob-prof-info">
            <div class="mob-prof-name">{{ $profileName }}</div>
            <div class="mob-prof-email">{{ $profileEmail }}</div>
        </div>
        <button class="mob-prof-settings" id="mobProfSettingsBtn" aria-label="Pengaturan">
            <i class="bi bi-gear"></i>
        </button>
    </div>

    <!-- Profile Summary Info -->
    <div class="mob-prof-summary">
        <div class="mob-prof-summary-item">
            <span class="mob-prof-summary-label">Email</span>
            <span class="mob-prof-summary-value">{{ $profileEmail }}</span>
        </div>
        @if($profilePhone)
        <div class="mob-prof-summary-item">
            <span class="mob-prof-summary-label">No. HP</span>
            <span class="mob-prof-summary-value">{{ $profilePhone }}</span>
        </div>
        @endif
    </div>

    <!-- Order Status Scroll -->
    <div class="mob-order-status-wrap">
        <div class="mob-order-status-scroll">
            <a href="{{ url('/orders') }}?status=waiting_payment" class="mob-order-status-item">
                <div class="mob-order-status-icon"><i class="bi bi-credit-card"></i></div>
                <span class="mob-order-status-label">Belum Bayar</span>
            </a>
            <a href="{{ url('/orders') }}?status=processing" class="mob-order-status-item">
                <div class="mob-order-status-icon"><i class="bi bi-box-seam"></i></div>
                <span class="mob-order-status-label">Dikemas</span>
            </a>
            <a href="{{ url('/orders') }}?status=shipping" class="mob-order-status-item">
                <div class="mob-order-status-icon"><i class="bi bi-truck"></i></div>
                <span class="mob-order-status-label">Dikirim</span>
            </a>
            <a href="{{ url('/orders') }}?status=completed" class="mob-order-status-item">
                <div class="mob-order-status-icon"><i class="bi bi-check-circle"></i></div>
                <span class="mob-order-status-label">Selesai</span>
            </a>
            @if(isset($is_sales) && $is_sales)
            <a href="{{ url('/orders') }}" class="mob-order-status-item">
                <div class="mob-order-status-icon"><i class="bi bi-clock-history"></i></div>
                <span class="mob-order-status-label">Riwayat</span>
            </a>
            @else
            <a href="{{ url('/orders') }}" class="mob-order-status-item mob-order-status-all">
                <div class="mob-order-status-icon"><i class="bi bi-grid-3x3-gap"></i></div>
                <span class="mob-order-status-label">Semua</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mob-prof-stats">
        <a href="{{ url('/orders') }}" class="mob-prof-stat-item">
            <span class="mob-prof-stat-num">{{ (int) ($order_stats['total_transaksi'] ?? ($recentOrders?->count() ?? 0)) }}</span>
            <span class="mob-prof-stat-label">Pesanan</span>
        </a>
        <a href="{{ url('/profile/addresses') }}" class="mob-prof-stat-item">
            <span class="mob-prof-stat-num">{{ (int) (($addresses ?? collect())->count()) }}</span>
            <span class="mob-prof-stat-label">Alamat</span>
        </a>
    </div>

    <!-- Recent Orders List (compact) -->
    @if(! (isset($is_sales) && $is_sales) && ($recentOrders ?? collect())->count() > 0)
    <div class="mob-prof-section">
        <div class="mob-prof-section-header">
            <span class="mob-prof-section-title">Pesanan Terbaru</span>
            <a href="{{ route('guest.orders.index') }}" class="mob-prof-section-link">Lihat Semua <i class="bi bi-chevron-right"></i></a>
        </div>
        @foreach(($recentOrders ?? collect())->take(3) as $order)
        <a href="{{ route('guest.orders.show', $order) }}" class="mob-prof-order-item">
            <div class="mob-prof-order-left">
                <span class="mob-prof-order-no">{{ $order->order_no }}</span>
                <span class="mob-prof-order-status status-badge-{{ strtolower($order->status) }}">{{ $order->status }}</span>
            </div>
            <div class="mob-prof-order-right">
                <span class="mob-prof-order-price">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</span>
                <i class="bi bi-chevron-right"></i>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</section>

<!-- Mobile: Settings Bottom Sheet -->
<div class="modal fade d-lg-none" id="mobSettingsSheet" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-bottom">
        <div class="modal-content" style="border-radius: 14px 14px 0 0;margin-top:auto;">
            <div class="modal-body p-0">
                <div class="mob-sheet-handle"></div>
                <div class="mob-sheet-title">Pengaturan</div>
                @if(isset($is_sales) && $is_sales)
                <a href="{{ route('guest.profile.my-customers.index') }}" class="mob-sheet-item">
                    <i class="bi bi-people"></i><span>Pelanggan Saya</span>
                </a>
                <a href="{{ route('guest.register-buyer') }}" class="mob-sheet-item">
                    <i class="bi bi-person-plus"></i><span>Tambah Buyer</span>
                </a>
                <a href="{{ route('guest.profile.logs') }}" class="mob-sheet-item">
                    <i class="bi bi-clock-history"></i><span>Log Aktivitas</span>
                </a>
                @else
                <a href="{{ url('/profile/addresses') }}" class="mob-sheet-item">
                    <i class="bi bi-geo-alt"></i><span>Alamat</span>
                </a>
                <a href="{{ route('guest.change-password') }}" class="mob-sheet-item">
                    <i class="bi bi-key"></i><span>Ubah Password</span>
                </a>
                @endif
                <div class="mob-sheet-divider"></div>
                <form method="POST" action="{{ route('guest.logout') }}" data-ajax="false" class="mob-sheet-item mob-sheet-logout">
                    @csrf
                    <button type="submit" style="background:none;border:none;display:flex;align-items:center;gap:10px;width:100%;padding:12px 16px;color:var(--danger-color);font-size:0.88rem;font-weight:600;">
                        <i class="bi bi-box-arrow-right"></i><span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var settingsBtn = document.getElementById('mobProfSettingsBtn');
    if (settingsBtn) {
        settingsBtn.addEventListener('click', function() {
            var sheetEl = document.getElementById('mobSettingsSheet');
            if (sheetEl) {
                var sheet = new bootstrap.Modal(sheetEl);
                sheet.show();
            }
        });
    }
});
</script>
@endpush
