@php
    $items = [
        ['label' => 'dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'icon' => 'bi-speedometer2'],
        ['label' => 'account', 'route' => 'admin.accounts.index', 'active' => 'admin.accounts.*', 'icon' => 'bi-people'],
        ['label' => 'customer', 'route' => 'admin.customers.index', 'active' => 'admin.customers.*', 'icon' => 'bi-person-lines-fill'],
        ['label' => 'product category', 'route' => 'admin.categories.index', 'active' => 'admin.categories.*', 'icon' => 'bi-tags'],
        ['label' => 'product brand', 'route' => 'admin.brands.index', 'active' => 'admin.brands.*', 'icon' => 'bi-award'],
        ['label' => 'product status', 'route' => 'admin.statuses.index', 'active' => 'admin.statuses.*', 'icon' => 'bi-flag'],
        ['label' => 'product', 'route' => 'admin.products.index', 'active' => 'admin.products.*', 'icon' => 'bi-box-seam'],
        ['label' => 'sales order', 'route' => 'admin.sales-orders.index', 'active' => 'admin.sales-orders.*', 'icon' => 'bi-receipt'],
        ['label' => 'broadcast', 'route' => 'admin.broadcasts.index', 'active' => 'admin.broadcasts.*', 'icon' => 'bi-megaphone'],
        ['label' => 'favorite brand', 'route' => 'admin.favorite-brands.index', 'active' => 'admin.favorite-brands.*', 'icon' => 'bi-heart'],
        ['label' => 'logbook', 'route' => 'admin.logs.index', 'active' => 'admin.logs.*', 'icon' => 'bi-journal-text'],
        ['label' => 'about us', 'route' => 'admin.about.edit', 'active' => 'admin.about.*', 'icon' => 'bi-info-circle'],
    ];
@endphp

<aside class="w-72 bg-white border-r border-slate-200 px-4 py-5">
    <div class="flex items-center gap-3 px-2 mb-6">
        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold">PBS</div>
        <div>
            <div class="text-sm font-semibold">ADMIN</div>
            <div class="text-xs text-slate-500">admin panel</div>
        </div>
    </div>

    <div class="px-2">
        <div class="text-xs uppercase tracking-wider text-slate-500 mb-2">Main</div>
        <nav class="space-y-1">
            @foreach ($items as $item)
                @php
                    $hasRoute = \Illuminate\Support\Facades\Route::has($item['route']);
                    $href = $hasRoute ? route($item['route']) : '#';
                    $active = request()->routeIs($item['active']);
                @endphp
                <a href="{{ $href }}" class="group flex items-center px-3 py-2 rounded-xl text-sm font-medium {{ $active ? 'bg-sky-100 text-slate-900' : 'hover:bg-slate-100 text-slate-700' }}">
                    <span class="flex items-center gap-3">
                        <i class="bi {{ $item['icon'] }} text-base leading-none {{ $active ? 'text-sky-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                        <span class="capitalize">{{ $item['label'] }}</span>
                    </span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
