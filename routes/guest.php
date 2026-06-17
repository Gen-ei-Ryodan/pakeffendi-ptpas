<?php

use App\Http\Controllers\Guest\AuthController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\CustomerAddressController;
use App\Http\Controllers\Guest\OrderController;
use App\Http\Controllers\Guest\ProfileController;
use App\Http\Controllers\Guest\RegionController;
use App\Models\Broadcast;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductStatus;
use Illuminate\Support\Facades\Route;

// Guest Routes - Modern Views
Route::prefix('/')->group(function () {
    // Home
    Route::get('/', function () {
        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $featuredProducts = Product::query()
            ->with(['brand:brand_code,brand_name'])
            ->active()
            ->activeCategory()
            ->hasPhoto()
            ->orderBy('name')
            ->limit(8)
            ->get();

        $statuses = ProductStatus::query()->orderBy('sort_order')->get();
        $statusProducts = collect();
        foreach ($statuses as $status) {
            $products = Product::query()
                ->with(['brand:brand_code,brand_name'])
                ->active()
                ->activeCategory()
                ->hasPhoto()
                ->byStatus($status->code)
                ->limit(8)
                ->get();
            if ($products->isNotEmpty()) {
                $statusProducts->push([
                    'status' => $status,
                    'products' => $products,
                ]);
            }
        }

        $broadcasts = Broadcast::query()
            ->latest()
            ->limit(8)
            ->get(['id', 'image_path', 'description', 'updated_at']);

        return view('guest.home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'statusProducts' => $statusProducts,
            'broadcasts' => $broadcasts,
        ]);
    });

    // Products
    Route::get('/products', function () {
        if (request()->input('category_id') === '') {
            request()->merge(['category_id' => null]);
        }
        if (request()->input('brand_id') === '') {
            request()->merge(['brand_id' => null]);
        }
        if (request()->input('sort') === '') {
            request()->merge(['sort' => null]);
        }

        $validated = request()->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'string', 'max:50'],
            'brand_id' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'string', 'in:price-asc,price-desc,name-asc,name-desc,newest,popular'],
        ]);

        $query = Product::query()
            ->with(['brand:brand_code,brand_name', 'category:category_code,name'])
            ->active()
            ->activeCategory()
            ->hasPhoto()
            ->orderBy('name');

        if (! empty($validated['q'])) {
            $q = trim((string) $validated['q']);
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        if (! empty($validated['category_id'])) {
            $query->where('product_category_code', $validated['category_id']);
        }

        if (! empty($validated['brand_id'])) {
            $query->where('product_brand_code', $validated['brand_id']);
        }

        if (! empty($validated['sort'])) {
            switch ($validated['sort']) {
                case 'price-asc':
                    $query->reorder()->orderBy('price_1')->orderBy('name');
                    break;
                case 'price-desc':
                    $query->reorder()->orderByDesc('price_1')->orderBy('name');
                    break;
                case 'name-asc':
                    $query->reorder()->orderBy('name');
                    break;
                case 'name-desc':
                    $query->reorder()->orderByDesc('name');
                    break;
                case 'popular':
                case 'newest':
                default:
                    $query->reorder()->orderByDesc('created_at');
                    break;
            }
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = ProductCategory::query()->where('is_active', true)->orderBy('name')->get(['category_code', 'name']);
        $brands = ProductBrand::query()->orderBy('brand_name')->get(['brand_code', 'brand_name']);

        return view('guest.products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => [
                'q' => $validated['q'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'brand_id' => $validated['brand_id'] ?? null,
                'sort' => $validated['sort'] ?? null,
            ],
        ]);
    });

    // Products Load More (JSON for infinite scroll)
    Route::get('/products/load-more', function () {
        $validated = request()->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'string', 'max:50'],
            'brand_id' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', 'string', 'in:price-asc,price-desc,name-asc,name-desc,newest,popular'],
        ]);

        $page = (int) ($validated['page'] ?? 1);

        $query = Product::query()
            ->with(['brand:brand_code,brand_name'])
            ->active()
            ->activeCategory()
            ->hasPhoto()
            ->orderBy('name');

        if (! empty($validated['q'])) {
            $q = trim((string) $validated['q']);
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        if (! empty($validated['category_id'])) {
            $query->where('product_category_code', $validated['category_id']);
        }

        if (! empty($validated['brand_id'])) {
            $query->where('product_brand_code', $validated['brand_id']);
        }

        if (! empty($validated['sort'])) {
            switch ($validated['sort']) {
                case 'price-asc':
                    $query->reorder()->orderBy('price_1')->orderBy('name');
                    break;
                case 'price-desc':
                    $query->reorder()->orderByDesc('price_1')->orderBy('name');
                    break;
                case 'name-asc':
                    $query->reorder()->orderBy('name');
                    break;
                case 'name-desc':
                    $query->reorder()->orderByDesc('name');
                    break;
                case 'popular':
                case 'newest':
                default:
                    $query->reorder()->orderByDesc('created_at');
                    break;
            }
        }

        $products = $query->paginate(12, ['*'], 'page', $page)->withQueryString();

        $html = '';
        foreach ($products as $product) {
            $html .= view('guest.partials.product-card-item', compact('product'))->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $products->hasMorePages(),
            'nextPage' => $page + 1,
        ]);
    });

    Route::get('/categories', function () {
        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['category_code', 'name', 'image_path']);

        return view('guest.categories.index', [
            'categories' => $categories,
        ]);
    });

    Route::get('/products/{id}', function ($id) {
        $product = Product::query()
            ->with([
                'brand:brand_code,brand_name',
                'category:category_code,name',
                'relatedProducts' => function ($q) {
                    $q->with(['brand:brand_code,brand_name'])->active()->activeCategory()->hasPhoto()->orderBy('name');
                },
            ])
            ->active()
            ->activeCategory()
            ->hasPhoto()
            ->findOrFail($id);

        $relatedProducts = $product->relatedProducts;

        return view('guest.products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    });

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->middleware('guest.auth')->name('guest.cart.index');
    Route::get('/cart/summary', [CartController::class, 'summary'])->name('guest.cart.summary');
    Route::post('/cart/items', [CartController::class, 'addItem'])->middleware('guest.auth')->name('guest.cart.items.store');
    Route::post('/cart/items/{product}', [CartController::class, 'setItemQuantity'])->middleware('guest.auth')->name('guest.cart.items.set');
    Route::delete('/cart/items/{product}', [CartController::class, 'removeItem'])->middleware('guest.auth')->name('guest.cart.items.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->middleware('guest.auth')->name('guest.cart.clear');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->middleware('guest.auth')->name('guest.cart.checkout');
    Route::get('/cart/customers/{customer}/addresses', [CartController::class, 'customerAddresses'])->middleware(['auth:web', 'sales'])->name('guest.cart.customer-addresses');
    Route::get('/cart/select-customer/{customerId}', [CartController::class, 'setActiveCustomer'])->middleware(['auth:web', 'sales'])->name('guest.cart.select-customer');
    Route::get('/cart/clear-customer', [CartController::class, 'clearActiveCustomer'])->middleware(['auth:web', 'sales'])->name('guest.cart.clear-customer');
    Route::get('/cart/my-customers', [CartController::class, 'myCustomers'])->middleware(['auth:web', 'sales'])->name('guest.cart.my-customers');

    // Auth
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('guest.login');
        Route::post('/login', [AuthController::class, 'login'])->name('guest.login.store');
    });

    // Email Verification
    Route::get('/verify-email', [AuthController::class, 'showVerifyEmail'])->name('guest.verify-email');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('guest.verify-email.store');
    Route::get('/verify-email/{code}', [AuthController::class, 'verifyEmailDirect'])->name('guest.verify-email.direct');

    // Register Buyer (by Sales/Admin)
    Route::middleware(['auth:web'])->group(function () {
        Route::get('/register-buyer', [AuthController::class, 'showRegisterBuyer'])->name('guest.register-buyer');
        Route::post('/register-buyer', [AuthController::class, 'registerBuyer'])->name('guest.register-buyer.store');
    });

    // Change Password
    Route::middleware('guest.auth')->group(function () {
        Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('guest.change-password');
        Route::post('/change-password/send-code', [AuthController::class, 'sendChangePasswordCode'])->name('guest.change-password.send-code');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('guest.change-password.store');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('guest.auth')->name('guest.logout');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->middleware('guest.auth')->name('guest.profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->middleware('guest.auth')->name('guest.profile.update');
    Route::get('/profile/logs', [ProfileController::class, 'logs'])->middleware('auth:web', 'sales')->name('guest.profile.logs');

    Route::middleware('guest.auth')->prefix('profile/addresses')->name('guest.profile.addresses.')->group(function () {
        Route::get('/', [CustomerAddressController::class, 'index'])->name('index');
        Route::post('/', [CustomerAddressController::class, 'store'])->name('store');
        Route::get('/{address}/edit', [CustomerAddressController::class, 'edit'])->name('edit');
        Route::put('/{address}', [CustomerAddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [CustomerAddressController::class, 'destroy'])->name('destroy');
        Route::post('/{address}/active', [CustomerAddressController::class, 'setActive'])->name('set-active');
    });

    Route::prefix('regions')->group(function () {
        Route::get('/provinces', [RegionController::class, 'provinces'])->name('guest.regions.provinces');
        Route::get('/regencies/{provinceCode}', [RegionController::class, 'regencies'])->name('guest.regions.regencies');
        Route::get('/districts/{regencyCode}', [RegionController::class, 'districts'])->name('guest.regions.districts');
        Route::get('/villages/{districtCode}', [RegionController::class, 'villages'])->name('guest.regions.villages');
    });

    // My Customers (Sales Only)
    Route::middleware(['auth:web', 'sales'])->prefix('profile/my-customers')->name('guest.profile.my-customers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Guest\MyCustomerController::class, 'index'])->name('index');
        Route::get('/{customer}', [\App\Http\Controllers\Guest\MyCustomerController::class, 'show'])->name('show');
    });

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->middleware('guest.auth')->name('guest.orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('guest.auth')->name('guest.orders.show');

    // About & Contact
    Route::get('/about', function () {
        return view('guest.about.index');
    });

    Route::get('/contact', function () {
        return view('guest.contact.index');
    });
});
