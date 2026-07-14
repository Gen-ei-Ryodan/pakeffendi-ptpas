@extends('guest.layouts.app')

@section('title', 'Detail Pelanggan - PAS Market')

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.my-customers.index') }}">Pelanggan Saya</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $myCustomer->full_name }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1 class="h3 fw-bold text-secondary mb-0">Detail Pelanggan</h1>
            <a href="{{ route('guest.profile.my-customers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Profile Sidebar -->
            @include('guest.partials.profile-sidebar')
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Customer Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Informasi Pelanggan</h5>
                        <div class="d-flex gap-1">
                            <a href="{{ route('guest.profile.my-customers.edit', $myCustomer) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit Data
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($pendingChanges ?? false)
                            <div class="alert alert-warning mb-3">
                                <i class="bi bi-hourglass-split me-2"></i>
                                Ada permintaan perubahan data yang menunggu approval admin. Perubahan akan diterapkan setelah disetujui.
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nama Lengkap</label>
                                <div class="fw-bold">{{ $myCustomer->full_name }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Kode Pelanggan</label>
                                <div class="fw-bold">{{ $myCustomer->customer_code }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Email</label>
                                <div>{{ $myCustomer->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Nomor HP</label>
                                <div>{{ $myCustomer->phone }}</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small">Alamat</label>
                                <div>{{ $myCustomer->address }}</div>
                                <div>{{ $myCustomer->city }}, {{ $myCustomer->province }} {{ $myCustomer->postal_code }}</div>
                            </div>
                            @if($myCustomer->google_maps_url)
                            <div class="col-12">
                                <label class="form-label text-muted small">Link Google Maps Toko</label>
                                <div>
                                    <a href="{{ $myCustomer->google_maps_url }}" target="_blank" rel="noopener" class="text-decoration-none">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>Lihat Lokasi Toko
                                    </a>
                                </div>
                            </div>
                            @endif
                            @if($myCustomer->store_photo_path)
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Foto Toko</label>
                                <div>
                                    <img src="{{ asset('storage/'.$myCustomer->store_photo_path) }}" alt="Foto Toko {{ $myCustomer->full_name }}" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label text-muted small">Status Akun</label>
                                <div>
                                    @if($myCustomer->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($myCustomer->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $myCustomer->status }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order History -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="fw-bold mb-0">Riwayat Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Pesanan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($orders as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_no }}</td>
                                        <td>{{ $order->order_date?->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                        </td>
                                        <td class="fw-semibold">Rp {{ number_format((float) $order->grand_total, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('guest.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Belum ada riwayat pesanan.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
