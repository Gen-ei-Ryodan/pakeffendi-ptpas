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

                <!-- Customer Addresses -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Alamat Pelanggan</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                            <i class="bi bi-plus-lg"></i> Tambah Alamat
                        </button>
                    </div>
                    <div class="card-body">
                        @php
                            $addresses = $myCustomer->addresses;
                        @endphp
                        <div class="row g-3">
                            @forelse($addresses as $addr)
                                <div class="col-12">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold">
                                                    {{ $addr->label ?: 'Alamat' }}
                                                    @if($addr->is_active)
                                                        <span class="badge bg-success ms-2">Aktif</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small">
                                                    {{ $addr->recipient_name ?: $myCustomer->full_name }}
                                                    @if($addr->phone)
                                                        · {{ $addr->phone }}
                                                    @endif
                                                </div>
                                                <div class="mt-2">{{ $addr->full_address }}</div>
                                            </div>
                                            <div class="d-flex gap-2 flex-shrink-0">
                                                @if(! $addr->is_active)
                                                    <form method="POST" action="{{ route('guest.profile.my-customers.addresses.set-active', [$myCustomer, $addr]) }}" data-ajax="false">
                                                        @csrf
                                                        <button class="btn btn-outline-success btn-sm" type="submit">Aktifkan</button>
                                                    </form>
                                                @endif

                                                <button type="button" class="btn btn-outline-secondary btn-sm edit-addr-btn"
                                                    data-id="{{ $addr->id }}"
                                                    data-label="{{ $addr->label }}"
                                                    data-recipient_name="{{ $addr->recipient_name }}"
                                                    data-phone="{{ $addr->phone }}"
                                                    data-address="{{ $addr->address }}"
                                                    data-province_code="{{ $addr->province_code }}"
                                                    data-province_name="{{ $addr->province_name }}"
                                                    data-regency_code="{{ $addr->regency_code }}"
                                                    data-regency_name="{{ $addr->regency_name }}"
                                                    data-district_code="{{ $addr->district_code }}"
                                                    data-district_name="{{ $addr->district_name }}"
                                                    data-village_code="{{ $addr->village_code }}"
                                                    data-village_name="{{ $addr->village_name }}"
                                                    data-postal_code="{{ $addr->postal_code }}"
                                                    data-is_active="{{ $addr->is_active ? '1' : '0' }}">
                                                    Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted text-center py-4">Belum ada alamat. Klik "Tambah Alamat" untuk menambahkan.</div>
                            @endforelse
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

<!-- Add Address Modal -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('guest.profile.my-customers.addresses.store', $myCustomer) }}" data-ajax="false">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Label</label>
                            <input type="text" name="label" class="form-control" placeholder="Rumah / Kantor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="recipient_name" class="form-control" placeholder="{{ $myCustomer->full_name }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. HP Penerima</label>
                            <input type="text" name="phone" class="form-control" placeholder="{{ $myCustomer->phone }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Provinsi</label>
                            <select name="province_code" class="form-select addr-province" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="province_name" class="addr-province-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select name="regency_code" class="form-select addr-regency" required disabled>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <input type="hidden" name="regency_name" class="addr-regency-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="district_code" class="form-select addr-district" required disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" name="district_name" class="addr-district-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Desa/Kelurahan</label>
                            <select name="village_code" class="form-select addr-village" required disabled>
                                <option value="">Pilih Desa</option>
                            </select>
                            <input type="hidden" name="village_name" class="addr-village-name">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Detail</label>
                            <textarea name="address" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="is_active" id="addIsActive" checked>
                                <label class="form-check-label" for="addIsActive">Jadikan alamat aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Address Modal -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Alamat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="editAddressForm" data-ajax="false">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Label</label>
                            <input type="text" name="label" class="form-control" id="editLabel" placeholder="Rumah / Kantor">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nama Penerima</label>
                            <input type="text" name="recipient_name" class="form-control" id="editRecipientName">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No. HP Penerima</label>
                            <input type="text" name="phone" class="form-control" id="editPhone">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Provinsi</label>
                            <select name="province_code" class="form-select edit-addr-province" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="province_name" class="edit-addr-province-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kabupaten/Kota</label>
                            <select name="regency_code" class="form-select edit-addr-regency" required>
                                <option value="">Pilih Kabupaten</option>
                            </select>
                            <input type="hidden" name="regency_name" class="edit-addr-regency-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Kecamatan</label>
                            <select name="district_code" class="form-select edit-addr-district" required>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                            <input type="hidden" name="district_name" class="edit-addr-district-name">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Desa/Kelurahan</label>
                            <select name="village_code" class="form-select edit-addr-village" required>
                                <option value="">Pilih Desa</option>
                            </select>
                            <input type="hidden" name="village_name" class="edit-addr-village-name">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" name="postal_code" class="form-control" id="editPostalCode">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat Detail</label>
                            <textarea name="address" class="form-control" rows="3" required id="editAddress"></textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="is_active" id="editIsActive">
                                <label class="form-check-label" for="editIsActive">Jadikan alamat aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:1px solid #e5e7eb;">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Region cascade for address modals
function initRegionCascade(prefix) {
    var provinceSel = document.querySelector('.' + prefix + '-province');
    var regencySel = document.querySelector('.' + prefix + '-regency');
    var districtSel = document.querySelector('.' + prefix + '-district');
    var villageSel = document.querySelector('.' + prefix + '-village');
    var provinceName = document.querySelector('.' + prefix + '-province-name');
    var regencyName = document.querySelector('.' + prefix + '-regency-name');
    var districtName = document.querySelector('.' + prefix + '-district-name');
    var villageName = document.querySelector('.' + prefix + '-village-name');

    function loadRegencies(provCode, selected) {
        regencySel.disabled = true;
        regencySel.innerHTML = '<option value="">Memuat...</option>';
        districtSel.disabled = true;
        districtSel.innerHTML = '<option value="">Pilih Kecamatan</option>';
        villageSel.disabled = true;
        villageSel.innerHTML = '<option value="">Pilih Desa</option>';

        fetch('/regions/regencies/' + provCode)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                regencySel.innerHTML = '<option value="">Pilih Kabupaten</option>';
                (data.data || []).forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    if (selected && item.code === selected) opt.selected = true;
                    regencySel.appendChild(opt);
                });
                regencySel.disabled = false;
                if (selected) regencySel.dispatchEvent(new Event('change', {bubbles: true}));
            });
    }

    function loadDistricts(regCode, selected) {
        districtSel.disabled = true;
        districtSel.innerHTML = '<option value="">Memuat...</option>';
        villageSel.disabled = true;
        villageSel.innerHTML = '<option value="">Pilih Desa</option>';

        fetch('/regions/districts/' + regCode)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                districtSel.innerHTML = '<option value="">Pilih Kecamatan</option>';
                (data.data || []).forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    if (selected && item.code === selected) opt.selected = true;
                    districtSel.appendChild(opt);
                });
                districtSel.disabled = false;
                if (selected) districtSel.dispatchEvent(new Event('change', {bubbles: true}));
            });
    }

    function loadVillages(distCode, selected) {
        villageSel.disabled = true;
        villageSel.innerHTML = '<option value="">Memuat...</option>';

        fetch('/regions/villages/' + distCode)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                villageSel.innerHTML = '<option value="">Pilih Desa</option>';
                (data.data || []).forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name;
                    if (selected && item.code === selected) opt.selected = true;
                    villageSel.appendChild(opt);
                });
                villageSel.disabled = false;
            });
    }

    provinceSel.addEventListener('change', function() {
        var selectedText = this.options[this.selectedIndex]?.text || '';
        provinceName.value = selectedText;
        if (this.value) loadRegencies(this.value, null);
        else {
            regencySel.disabled = true; regencySel.innerHTML = '<option value="">Pilih Kabupaten</option>';
            districtSel.disabled = true; districtSel.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSel.disabled = true; villageSel.innerHTML = '<option value="">Pilih Desa</option>';
        }
    });

    regencySel.addEventListener('change', function() {
        var selectedText = this.options[this.selectedIndex]?.text || '';
        regencyName.value = selectedText;
        if (this.value) loadDistricts(this.value, null);
        else {
            districtSel.disabled = true; districtSel.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSel.disabled = true; villageSel.innerHTML = '<option value="">Pilih Desa</option>';
        }
    });

    districtSel.addEventListener('change', function() {
        var selectedText = this.options[this.selectedIndex]?.text || '';
        districtName.value = selectedText;
        if (this.value) loadVillages(this.value, null);
        else {
            villageSel.disabled = true; villageSel.innerHTML = '<option value="">Pilih Desa</option>';
        }
    });

    villageSel.addEventListener('change', function() {
        var selectedText = this.options[this.selectedIndex]?.text || '';
        villageName.value = selectedText;
    });

    // Load provinces
    fetch('/regions/provinces')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            provinceSel.innerHTML = '<option value="">Pilih Provinsi</option>';
            (data.data || []).forEach(function(item) {
                var opt = document.createElement('option');
                opt.value = item.code;
                opt.textContent = item.name;
                provinceSel.appendChild(opt);
            });
            var selectedProv = provinceSel.getAttribute('data-selected');
            if (selectedProv) {
                provinceSel.value = selectedProv;
                provinceSel.dispatchEvent(new Event('change', {bubbles: true}));
            }
        });
}

// Edit button handler
document.addEventListener('DOMContentLoaded', function() {
    initRegionCascade('addr');

    document.querySelectorAll('.edit-addr-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = document.getElementById('editAddressForm');
            form.action = '{{ route('guest.profile.my-customers.addresses.update', [$myCustomer, 'ADDR_ID']) }}'.replace('ADDR_ID', this.dataset.id);

            document.getElementById('editLabel').value = this.dataset.label || '';
            document.getElementById('editRecipientName').value = this.dataset.recipient_name || '';
            document.getElementById('editPhone').value = this.dataset.phone || '';
            document.getElementById('editAddress').value = this.dataset.address || '';
            document.getElementById('editPostalCode').value = this.dataset.postal_code || '';
            document.getElementById('editIsActive').checked = this.dataset.is_active === '1';

            // Set province selected and cascade
            var provSel = document.querySelector('.edit-addr-province');
            provSel.setAttribute('data-selected', this.dataset.province_code);
            var regSel = document.querySelector('.edit-addr-regency');
            regSel.setAttribute('data-selected', this.dataset.regency_code);
            var disSel = document.querySelector('.edit-addr-district');
            disSel.setAttribute('data-selected', this.dataset.district_code);
            var vilSel = document.querySelector('.edit-addr-village');
            vilSel.setAttribute('data-selected', this.dataset.village_code);

            document.querySelector('.edit-addr-province-name').value = this.dataset.province_name || '';
            document.querySelector('.edit-addr-regency-name').value = this.dataset.regency_name || '';
            document.querySelector('.edit-addr-district-name').value = this.dataset.district_name || '';
            document.querySelector('.edit-addr-village-name').value = this.dataset.village_name || '';

            // Re-init edit cascade
            var editModal = document.getElementById('editAddressModal');
            editModal.addEventListener('shown.bs.modal', function() {
                var oldProv = document.querySelector('.edit-addr-province');
                oldProv.innerHTML = '<option value="">Pilih Provinsi</option>';
                initRegionCascade('edit-addr');
            }, {once: true});

            var modal = new bootstrap.Modal(editModal);
            modal.show();
        });
    });
});
</script>
@endpush
@endsection
