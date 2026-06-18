<div class="col-lg-3 mb-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-4">
            <div class="mb-3">
                <img src="https://via.placeholder.com/100x100/f8f9fa/333333?text=User" 
                        alt="Profile" class="rounded-circle mb-3" width="100" height="100">
                <h5 class="fw-bold mb-1">{{ isset($is_sales) && $is_sales ? $customer->name : $customer->full_name }}</h5>
                <p class="text-muted small mb-3">{{ $customer->email }}</p>
            </div>
            
            <div class="list-group list-group-flush text-start">
                <a href="{{ route('guest.profile.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.profile.index') ? 'active' : '' }}">
                    <i class="bi bi-person me-2"></i>Profil
                </a>
                <a href="{{ route('guest.orders.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.orders.index') || request()->routeIs('guest.orders.show') ? 'active' : '' }}">
                    <i class="bi bi-box me-2"></i>{{ isset($is_sales) && $is_sales ? 'Riwayat Order' : 'Pesanan' }}
                </a>

                @if(! (isset($is_sales) && $is_sales))
                    <a href="{{ url('/profile/addresses') }}" class="list-group-item list-group-item-action {{ request()->is('profile/addresses*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt me-2"></i>Alamat
                    </a>
                @endif
                
                @if(isset($is_sales) && $is_sales)
                    <a href="{{ route('guest.profile.my-customers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.profile.my-customers.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i>Pelanggan Saya
                    </a>
                    <a href="{{ route('guest.register-buyer') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.register-buyer') ? 'active' : '' }}">
                        <i class="bi bi-person-plus me-2"></i>Tambah Buyer
                    </a>
                    <a href="{{ route('guest.profile.logs') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.profile.logs') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i>Log Aktivitas
                    </a>
                @else
                    <a href="{{ route('guest.change-password') }}" class="list-group-item list-group-item-action {{ request()->routeIs('guest.change-password') ? 'active' : '' }}">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </a>
                @endif

                <form method="POST" action="{{ route('guest.logout') }}" class="d-grid mt-3" data-ajax="false">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
