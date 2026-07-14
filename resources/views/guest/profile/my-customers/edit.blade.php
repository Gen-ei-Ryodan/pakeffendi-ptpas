@extends('guest.layouts.app')

@section('title', 'Edit Pelanggan - PAS Market')

@section('content')
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.my-customers.index') }}">Pelanggan Saya</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.my-customers.show', $myCustomer) }}">{{ $myCustomer->full_name }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1 class="h3 fw-bold text-secondary mb-0">Edit Data Pelanggan</h1>
            <a href="{{ route('guest.profile.my-customers.show', $myCustomer) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row">
            @include('guest.partials.profile-sidebar')

            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent">
                        <h5 class="fw-bold mb-0">Form Edit Data Pelanggan</h5>
                        <p class="text-muted small mb-0 mt-1">Perubahan data akan dikirim ke admin untuk approval terlebih dahulu.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('guest.profile.my-customers.update', $myCustomer) }}" enctype="multipart/form-data" data-ajax="false">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $myCustomer->full_name) }}" required>
                                    @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $myCustomer->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $myCustomer->phone) }}" required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NPWP</label>
                                    <input type="text" name="npwp" class="form-control @error('npwp') is-invalid @enderror" value="{{ old('npwp', $myCustomer->npwp) }}">
                                    @error('npwp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">No. KTP</label>
                                    <input type="text" name="ktp_number" class="form-control @error('ktp_number') is-invalid @enderror" value="{{ old('ktp_number', $myCustomer->ktp_number) }}">
                                    @error('ktp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $myCustomer->address) }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" name="province" class="form-control" value="{{ old('province', $myCustomer->province) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kota</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city', $myCustomer->city) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $myCustomer->postal_code) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Link Google Maps Toko</label>
                                    <input type="url" name="google_maps_url" class="form-control @error('google_maps_url') is-invalid @enderror" value="{{ old('google_maps_url', $myCustomer->google_maps_url) }}" placeholder="https://maps.google.com/...">
                                    @error('google_maps_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Foto Toko</label>
                                    <input type="file" name="store_photo" class="form-control @error('store_photo') is-invalid @enderror" accept="image/*">
                                    @error('store_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if($myCustomer->store_photo_path)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/'.$myCustomer->store_photo_path) }}" alt="Foto Toko" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4">
                            <h6 class="fw-bold mb-3">Ubah Password (opsional)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 8 karakter">
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i>Kirim Permintaan Perubahan
                                </button>
                                <a href="{{ route('guest.profile.my-customers.show', $myCustomer) }}" class="btn btn-light ms-2">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
