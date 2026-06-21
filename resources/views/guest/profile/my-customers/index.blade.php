@extends('guest.layouts.app')

@section('title', 'Pelanggan Saya - PAS Market')

@section('content')
<!-- Page Header -->
<section class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('guest.profile.index') }}">Profil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pelanggan Saya</li>
            </ol>
        </nav>
        
        <h1 class="h3 fw-bold text-secondary mt-3 mb-0">Pelanggan Saya</h1>
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Daftar Pelanggan</h5>
                        
                        <form method="get" class="d-flex">
                            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm me-2" placeholder="Cari pelanggan...">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Kontak</th>
                                        <th class="text-center">Total Order</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $cust)
                                    <tr>
                                        <td class="fw-semibold">{{ $cust->customer_code }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $cust->full_name }}</div>
                                            <small class="text-muted">{{ $cust->email }}</small>
                                        </td>
                                        <td>{{ $cust->phone }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info rounded-pill">{{ $cust->total_orders }}</span>
                                        </td>
                                        <td>
                                            @if($cust->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($cust->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $cust->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('guest.profile.my-customers.show', $cust) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <form method="POST" action="{{ route('guest.profile.my-customers.destroy', $cust) }}" data-ajax="false" onsubmit="return confirm('Yakin ingin menghapus customer {{ $cust->full_name }}? Semua data terkait akan ikut terhapus.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-people display-4 d-block mb-3"></i>
                                            Belum ada pelanggan yang terdaftar.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-4">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
