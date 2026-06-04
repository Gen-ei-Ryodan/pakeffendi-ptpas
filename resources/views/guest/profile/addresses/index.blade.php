@extends('guest.layouts.app')

@section('title', 'Alamat Saya - PAS Market')

@section('content')
<section class="bg-light py-4 mobile-hide">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Alamat</li>
            </ol>
        </nav>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <h1 class="h3 fw-bold text-secondary mb-0">Alamat Saya</h1>
            <a href="{{ route('guest.profile.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</section>

<!-- Desktop Content -->
<section class="py-5 mobile-hide">
    <div class="container">
        <div class="row">
            @include('guest.partials.profile-sidebar')

            <div class="col-lg-9">
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

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent">
                        <h5 class="fw-bold mb-0">Tambah Alamat</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('guest.profile.addresses.store') }}" data-ajax="false">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Label</label>
                                    <input type="text" name="label" value="{{ old('label') }}" class="form-control" placeholder="Rumah / Kantor">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Penerima</label>
                                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" class="form-control" placeholder="{{ $customer->full_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">No. HP Penerima</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="{{ $customer->phone }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Provinsi</label>
                                    <select id="province" name="province_code" class="form-select" required data-selected="{{ old('province_code') }}">
                                        <option value="">Pilih Provinsi</option>
                                    </select>
                                    <input type="hidden" name="province_name" value="{{ old('province_name') }}" id="province_name">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kabupaten/Kota</label>
                                    <select id="regency" name="regency_code" class="form-select" required data-selected="{{ old('regency_code') }}" disabled>
                                        <option value="">Pilih Kabupaten</option>
                                    </select>
                                    <input type="hidden" name="regency_name" value="{{ old('regency_name') }}" id="regency_name">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Kecamatan</label>
                                    <select id="district" name="district_code" class="form-select" required data-selected="{{ old('district_code') }}" disabled>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <input type="hidden" name="district_name" value="{{ old('district_name') }}" id="district_name">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Desa/Kelurahan</label>
                                    <select id="village" name="village_code" class="form-select" required data-selected="{{ old('village_code') }}" disabled>
                                        <option value="">Pilih Desa</option>
                                    </select>
                                    <input type="hidden" name="village_name" value="{{ old('village_name') }}" id="village_name">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code') }}" class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Alamat Detail</label>
                                    <textarea name="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="isActive" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isActive">Jadikan alamat aktif</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-2"></i>Simpan Alamat
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Daftar Alamat</h5>
                    </div>
                    <div class="card-body">
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
                                                    {{ $addr->recipient_name ?: $customer->full_name }}
                                                    @if($addr->phone)
                                                        · {{ $addr->phone }}
                                                    @endif
                                                </div>
                                                <div class="mt-2">{{ $addr->full_address }}</div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                @if(! $addr->is_active)
                                                    <form method="POST" action="{{ route('guest.profile.addresses.set-active', $addr) }}" data-ajax="false">
                                                        @csrf
                                                        <button class="btn btn-outline-success btn-sm" type="submit">Aktifkan</button>
                                                    </form>
                                                @endif

                                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('guest.profile.addresses.edit', $addr) }}">
                                                    Edit
                                                </a>

                                                <form method="POST" action="{{ route('guest.profile.addresses.destroy', $addr) }}" data-ajax="false" onsubmit="return confirm('Hapus alamat ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger btn-sm" type="submit">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted text-center py-4">Belum ada alamat.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MOBILE ADDRESSES ====================== -->
<section class="d-lg-none" id="mobAddressSection">
    <div class="mob-addr-header">
        <h1 class="mob-addr-title">Alamat Saya</h1>
        <button class="mob-addr-add-btn" id="mobAddrToggleForm">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    @if(session('status'))
        <div class="mob-addr-alert">{{ session('status') }}</div>
    @endif

    <!-- Add Address Form (collapsible) -->
    <div class="mob-addr-form" id="mobAddrForm" style="display:none;">
        <form method="POST" action="{{ route('guest.profile.addresses.store') }}" data-ajax="false">
            @csrf
            <div class="mob-addr-form-inner">
                <div class="mob-addr-field half">
                    <label>Label</label>
                    <input type="text" name="label" value="{{ old('label') }}" placeholder="Rumah / Kantor">
                </div>
                <div class="mob-addr-field half">
                    <label>Nama Penerima</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}" placeholder="{{ $customer->full_name }}">
                </div>
                <div class="mob-addr-field half">
                    <label>No. HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="{{ $customer->phone }}">
                </div>
                <div class="mob-addr-field half">
                    <label>Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}">
                </div>
                <div class="mob-addr-field full">
                    <label>Provinsi</label>
                    <select id="mobProvince" name="province_code" required data-selected="{{ old('province_code') }}">
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <input type="hidden" name="province_name" value="{{ old('province_name') }}" id="mobProvinceName">
                </div>
                <div class="mob-addr-field full">
                    <label>Kabupaten/Kota</label>
                    <select id="mobRegency" name="regency_code" required data-selected="{{ old('regency_code') }}" disabled>
                        <option value="">Pilih Kabupaten</option>
                    </select>
                    <input type="hidden" name="regency_name" value="{{ old('regency_name') }}" id="mobRegencyName">
                </div>
                <div class="mob-addr-field full">
                    <label>Kecamatan</label>
                    <select id="mobDistrict" name="district_code" required data-selected="{{ old('district_code') }}" disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <input type="hidden" name="district_name" value="{{ old('district_name') }}" id="mobDistrictName">
                </div>
                <div class="mob-addr-field full">
                    <label>Desa/Kelurahan</label>
                    <select id="mobVillage" name="village_code" required data-selected="{{ old('village_code') }}" disabled>
                        <option value="">Pilih Desa</option>
                    </select>
                    <input type="hidden" name="village_name" value="{{ old('village_name') }}" id="mobVillageName">
                </div>
                <div class="mob-addr-field full">
                    <label>Alamat Detail</label>
                    <textarea name="address" rows="2" required>{{ old('address') }}</textarea>
                </div>
                <div class="mob-addr-field full">
                    <label class="mob-addr-check">
                        <input type="checkbox" value="1" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <span>Jadikan alamat aktif</span>
                    </label>
                </div>
                <button type="submit" class="mob-addr-submit">Simpan Alamat</button>
            </div>
        </form>
    </div>

    @if($errors->any())
    <div class="mob-addr-errors">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif

    <!-- Address List -->
    <div class="mob-addr-list">
        @forelse($addresses as $addr)
        <div class="mob-addr-card">
            <div class="mob-addr-card-top">
                <div class="mob-addr-card-label">
                    {{ $addr->label ?: 'Alamat' }}
                    @if($addr->is_active)
                        <span class="mob-addr-badge">Aktif</span>
                    @endif
                </div>
            </div>
            <div class="mob-addr-card-recipient">
                {{ $addr->recipient_name ?: $customer->full_name }}
                @if($addr->phone)
                    · {{ $addr->phone }}
                @endif
            </div>
            <div class="mob-addr-card-detail">{{ $addr->full_address }}</div>
            <div class="mob-addr-card-actions">
                @if(! $addr->is_active)
                <form method="POST" action="{{ route('guest.profile.addresses.set-active', $addr) }}" data-ajax="false">
                    @csrf
                    <button class="mob-addr-action active-btn" type="submit">Aktifkan</button>
                </form>
                @endif
                <a href="{{ route('guest.profile.addresses.edit', $addr) }}" class="mob-addr-action edit-btn">Edit</a>
                <form method="POST" action="{{ route('guest.profile.addresses.destroy', $addr) }}" data-ajax="false" onsubmit="return confirm('Hapus alamat ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="mob-addr-action delete-btn" type="submit">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="mob-addr-empty">
            <i class="bi bi-geo-alt"></i>
            <p>Belum ada alamat</p>
        </div>
        @endforelse
    </div>
</section>

<script>
    (function () {
        function bindRegionSelects(prefix) {
            const provinceId = prefix ? prefix + 'Province' : 'province';
            const regencyId = prefix ? prefix + 'Regency' : 'regency';
            const districtId = prefix ? prefix + 'District' : 'district';
            const villageId = prefix ? prefix + 'Village' : 'village';
            const provinceNameId = prefix ? prefix + 'ProvinceName' : 'province_name';
            const regencyNameId = prefix ? prefix + 'RegencyName' : 'regency_name';
            const districtNameId = prefix ? prefix + 'DistrictName' : 'district_name';
            const villageNameId = prefix ? prefix + 'VillageName' : 'village_name';

            const provinceSelect = document.getElementById(provinceId);
            const regencySelect = document.getElementById(regencyId);
            const districtSelect = document.getElementById(districtId);
            const villageSelect = document.getElementById(villageId);

            const provinceNameInput = document.getElementById(provinceNameId);
            const regencyNameInput = document.getElementById(regencyNameId);
            const districtNameInput = document.getElementById(districtNameId);
            const villageNameInput = document.getElementById(villageNameId);

            if (!provinceSelect || !regencySelect || !districtSelect || !villageSelect) return;

            const cache = new Map();
            async function fetchJson(url) {
                if (cache.has(url)) return cache.get(url);
                const res = await fetch(url);
                if (!res.ok) throw new Error('Fetch failed: ' + res.status);
                const json = await res.json();
                cache.set(url, json);
                return json;
            }

            function resetSelect(select, placeholder) {
                select.innerHTML = '<option value="">' + placeholder + '</option>';
                select.disabled = true;
            }

            function setLoading(select) {
                select.innerHTML = '<option value="">Loading...</option>';
                select.disabled = true;
            }

            function fillOptions(select, placeholder, items) {
                select.innerHTML = '<option value="">' + placeholder + '</option>';
                items.forEach(function(it) {
                    var opt = document.createElement('option');
                    opt.value = it.code;
                    opt.textContent = it.name;
                    select.appendChild(opt);
                });
                select.disabled = false;
            }

            function syncName(select, hiddenInput) {
                var opt = select.options[select.selectedIndex];
                hiddenInput.value = opt && opt.value ? (opt.textContent || '') : '';
            }

            async function loadProvinces() {
                setLoading(provinceSelect);
                resetSelect(regencySelect, 'Pilih Kabupaten');
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');
                provinceNameInput.value = '';
                regencyNameInput.value = '';
                districtNameInput.value = '';
                villageNameInput.value = '';
                var json = await fetchJson('{{ url('/regions/provinces') }}');
                fillOptions(provinceSelect, 'Pilih Provinsi', json.data || []);
            }

            async function loadRegencies(provinceCode) {
                setLoading(regencySelect);
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');
                regencyNameInput.value = '';
                districtNameInput.value = '';
                villageNameInput.value = '';
                var json = await fetchJson('{{ url('/regions/regencies') }}/' + encodeURIComponent(provinceCode));
                fillOptions(regencySelect, 'Pilih Kabupaten', json.data || []);
            }

            async function loadDistricts(regencyCode) {
                setLoading(districtSelect);
                resetSelect(villageSelect, 'Pilih Desa');
                districtNameInput.value = '';
                villageNameInput.value = '';
                var json = await fetchJson('{{ url('/regions/districts') }}/' + encodeURIComponent(regencyCode));
                fillOptions(districtSelect, 'Pilih Kecamatan', json.data || []);
            }

            async function loadVillages(districtCode) {
                setLoading(villageSelect);
                villageNameInput.value = '';
                var json = await fetchJson('{{ url('/regions/villages') }}/' + encodeURIComponent(districtCode));
                fillOptions(villageSelect, 'Pilih Desa', json.data || []);
            }

            provinceSelect.addEventListener('change', async function(e) {
                var code = e.target.value;
                syncName(provinceSelect, provinceNameInput);
                resetSelect(regencySelect, 'Pilih Kabupaten');
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');
                if (!code) return;
                try { await loadRegencies(code); } catch (_) { resetSelect(regencySelect, 'Gagal load'); }
            });

            regencySelect.addEventListener('change', async function(e) {
                var code = e.target.value;
                syncName(regencySelect, regencyNameInput);
                resetSelect(districtSelect, 'Pilih Kecamatan');
                resetSelect(villageSelect, 'Pilih Desa');
                if (!code) return;
                try { await loadDistricts(code); } catch (_) { resetSelect(districtSelect, 'Gagal load'); }
            });

            districtSelect.addEventListener('change', async function(e) {
                var code = e.target.value;
                syncName(districtSelect, districtNameInput);
                resetSelect(villageSelect, 'Pilih Desa');
                if (!code) return;
                try { await loadVillages(code); } catch (_) { resetSelect(villageSelect, 'Gagal load'); }
            });

            villageSelect.addEventListener('change', function() {
                syncName(villageSelect, villageNameInput);
            });

            (async function init() {
                try { await loadProvinces(); } catch (_) { resetSelect(provinceSelect, 'Gagal load'); return; }
                var ps = provinceSelect.dataset.selected || '';
                var rs = regencySelect.dataset.selected || '';
                var ds = districtSelect.dataset.selected || '';
                var vs = villageSelect.dataset.selected || '';
                if (ps) { provinceSelect.value = ps; syncName(provinceSelect, provinceNameInput); try { await loadRegencies(ps); } catch (_) { return; } }
                if (rs) { regencySelect.value = rs; syncName(regencySelect, regencyNameInput); try { await loadDistricts(rs); } catch (_) { return; } }
                if (ds) { districtSelect.value = ds; syncName(districtSelect, districtNameInput); try { await loadVillages(ds); } catch (_) { return; } }
                if (vs) { villageSelect.value = vs; syncName(villageSelect, villageNameInput); }
            })();
        }

        bindRegionSelects('');
        bindRegionSelects('mob');

        var toggleBtn = document.getElementById('mobAddrToggleForm');
        var formEl = document.getElementById('mobAddrForm');
        if (toggleBtn && formEl) {
            toggleBtn.addEventListener('click', function() {
                formEl.style.display = formEl.style.display === 'none' ? 'block' : 'none';
            });
        }
    })();
</script>
@endsection
