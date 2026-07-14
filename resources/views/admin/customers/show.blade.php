@extends('admin.layouts.app')

@section('title', 'Detail Customer - '.$customer->full_name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
<li class="breadcrumb-item active">{{ $customer->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        {{-- Left Column --}}
        <div class="col-lg-8">
            {{-- Customer Info Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Informasi Customer</h5>
                    <div>
                        <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                            <i class="bi bi-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Lengkap</label>
                            <div class="fw-bold fs-5">{{ $customer->full_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Kode Customer</label>
                            <div class="fw-bold">{{ $customer->customer_code }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Email</label>
                            <div>{{ $customer->email }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">No HP</label>
                            <div>{{ $customer->phone }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Contact Person</label>
                            <div>{{ $customer->contact_person }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Nama Perusahaan</label>
                            <div>{{ $customer->company_name ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">NPWP</label>
                            <div>{{ $customer->npwp ?: '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">No KTP</label>
                            <div>{{ $customer->ktp_number ?: '-' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Alamat</label>
                            <div>{{ $customer->address ?: '-' }}</div>
                            <div class="text-muted small">{{ $customer->city }}, {{ $customer->province }} {{ $customer->postal_code }}</div>
                        </div>
                        @if($customer->google_maps_url)
                        <div class="col-12">
                            <label class="form-label text-muted small">Link Google Maps Toko</label>
                            <div>
                                <a href="{{ $customer->google_maps_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-geo-alt-fill me-1"></i>Lihat Lokasi Toko
                                </a>
                            </div>
                        </div>
                        @endif
                        @if($customer->store_photo_path)
                        <div class="col-12">
                            <label class="form-label text-muted small">Foto Toko</label>
                            <div>
                                <img src="{{ asset('storage/'.$customer->store_photo_path) }}" alt="Foto Toko" class="img-thumbnail" style="max-height: 250px;">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Addresses --}}
            @if($customer->addresses->count())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent">
                    <h5 class="fw-bold mb-0">Daftar Alamat</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Penerima</th>
                                    <th>Alamat</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->addresses as $addr)
                                <tr>
                                    <td>{{ $addr->label ?: '-' }}</td>
                                    <td>{{ $addr->recipient_name ?: $customer->full_name }}</td>
                                    <td>{{ $addr->full_address }}</td>
                                    <td>
                                        @if($addr->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent">
                    <h5 class="fw-bold mb-0">Status & Sales</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Status Akun</span>
                            @if($customer->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($customer->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @else
                                <span class="badge bg-secondary">{{ $customer->status }}</span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Sales</span>
                            <span>{{ $customer->sales?->name ?? 'Mandiri' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Internal Code</span>
                            <span>{{ $customer->internal_code ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Terdaftar Sejak</span>
                            <span>{{ $customer->created_at->format('d M Y') }}</span>
                        </li>
                    </ul>

                    @if($customer->status === 'pending')
                    <div class="mt-3 d-flex gap-2">
                        <form method="post" action="{{ route('admin.customers.approve', $customer) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form method="post" action="{{ route('admin.customers.reject', $customer) }}">
                            @csrf @method('PATCH')
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            @if($pendingChanges ?? false)
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <h6 class="fw-bold text-warning">
                        <i class="bi bi-hourglass-split me-1"></i>Permintaan Perubahan
                    </h6>
                    <p class="small text-muted mb-2">Diajukan oleh: {{ $pendingChanges->sales?->name }}</p>
                    <p class="small text-muted mb-3">{{ $pendingChanges->created_at->format('d M Y H:i') }}</p>
                    <a href="{{ route('admin.customers.change-requests.show', $pendingChanges) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-eye me-1"></i>Review
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
