<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CartController extends Controller
{
    public const SALES_CUSTOMER_COOKIE = 'pas_sales_cid';

    public function __construct(
        private readonly CartService $cartService
    ) {}

    private function getShopper()
    {
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->isSales()) {
            return Auth::guard('web')->user();
        }

        return null;
    }

    /**
     * Resolve the cart for the current shopper context.
     * For sales users, checks the selected customer cookie to determine which customer's cart to use.
     *
     * @return array{cart: Cart, cookie: mixed, customer: Customer|null, salesId: int|null}
     */
    private function resolveCart(Request $request): array
    {
        $shopper = $this->getShopper();

        if ($shopper instanceof Customer) {
            $resolved = $this->cartService->resolve($request, $shopper);

            return [
                'cart' => $resolved['cart'],
                'cookie' => $resolved['cookie'],
                'customer' => $shopper,
                'salesId' => null,
            ];
        }

        if ($shopper instanceof User && $shopper->isSales()) {
            $customerId = (int) $request->cookie(self::SALES_CUSTOMER_COOKIE, '0');
            if ($customerId > 0) {
                $customer = Customer::where('id', $customerId)
                    ->where('sales_id', $shopper->id)
                    ->first();

                if ($customer) {
                    $resolved = $this->cartService->resolve($request, $customer, (int) $shopper->id);

                    return [
                        'cart' => $resolved['cart'],
                        'cookie' => $resolved['cookie'],
                        'customer' => $customer,
                        'salesId' => (int) $shopper->id,
                    ];
                }
            }

            // Sales logged in but no customer selected — use session cart (guest-like)
            $resolved = $this->cartService->resolve($request, null);

            return [
                'cart' => $resolved['cart'],
                'cookie' => $resolved['cookie'],
                'customer' => null,
                'salesId' => null,
            ];
        }

        // Guest
        $resolved = $this->cartService->resolve($request, null);

        return [
            'cart' => $resolved['cart'],
            'cookie' => $resolved['cookie'],
            'customer' => null,
            'salesId' => null,
        ];
    }

    public function index(Request $request)
    {
        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart']->load(['items.product.brand']);
        $shopper = $this->getShopper();

        $summary = $this->buildSummary($cart->items);

        $isSales = ($shopper instanceof User && $shopper->isSales());
        $myCustomers = $isSales ? Customer::where('sales_id', $shopper->id)->orderBy('full_name')->get() : collect();
        $addresses = collect();
        $activeAddressId = null;
        $selectedCustomer = $resolved['customer'];

        if ($shopper instanceof Customer) {
            $addresses = CustomerAddress::query()
                ->where('customer_id', $shopper->id)
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
            $activeAddressId = $addresses->firstWhere('is_active', true)?->id;
        } elseif ($isSales && $selectedCustomer) {
            $addresses = CustomerAddress::query()
                ->where('customer_id', $selectedCustomer->id)
                ->orderByDesc('is_active')
                ->orderByDesc('id')
                ->get();
            $activeAddressId = $addresses->firstWhere('is_active', true)?->id;
        }

        return response()
            ->view('guest.cart.index', [
                'cart' => $cart,
                'summary' => $summary,
                'customer' => $shopper,
                'selected_customer' => $selectedCustomer,
                'is_sales' => $isSales,
                'my_customers' => $myCustomers,
                'addresses' => $addresses,
                'active_address_id' => $activeAddressId,
            ])
            ->cookie($resolved['cookie']);
    }

    public function summary(Request $request)
    {
        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart']->load(['items.product']);

        $summary = $this->buildSummary($cart->items);

        return response()
            ->json(['summary' => $summary])
            ->cookie($resolved['cookie']);
    }

    public function addItem(Request $request)
    {
        Log::info('Adding item to cart', $request->all());

        try {
            $rules = [
                'product_id' => ['required', 'integer', 'exists:products,id'],
                'quantity' => ['nullable', 'integer', 'min:1', 'max:9999'],
            ];

            $shopper = $this->getShopper();

            $validated = $request->validate($rules);

            // For sales: resolve cart using the customer already selected on the cart page (from cookie)
            // Sales must select a customer once on the cart page before adding items
            if ($shopper instanceof User && $shopper->isSales()) {
                $customerId = (int) $request->cookie(self::SALES_CUSTOMER_COOKIE, '0');
                if ($customerId <= 0) {
                    abort(422, 'Silakan pilih customer terlebih dahulu di halaman keranjang.');
                }

                $customer = Customer::where('id', $customerId)
                    ->where('sales_id', $shopper->id)
                    ->first();

                if (! $customer) {
                    abort(422, 'Customer tidak ditemukan atau bukan milik Anda.');
                }

                $resolved = $this->cartService->resolve($request, $customer, (int) $shopper->id);
            } else {
                $resolved = $this->resolveCart($request);
            }

            $cart = $resolved['cart'];
            Log::info('Cart resolved', ['cart_id' => $cart->id, 'session_id' => $cart->session_id]);

            $product = Product::query()
                ->where('discontinued', false)
                ->whereHas('category', fn ($q) => $q->where('is_active', true))
                ->findOrFail($validated['product_id']);

            $this->cartService->addItem($cart, $product, (int) ($validated['quantity'] ?? 1));

            $cart->load(['items.product']);
            $summary = $this->buildSummary($cart->items);

            Log::info('Item added successfully');

            $response = $request->wantsJson()
                ? response()->json(['summary' => $summary], 201)
                : redirect()->to('/cart');

            return $response->cookie($resolved['cookie']);
        } catch (\Exception $e) {
            Log::error('Error adding item to cart: '.$e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    public function setItemQuantity(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        abort_if($product->discontinued, 404);

        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart'];

        $this->cartService->setItemQuantity($cart, $product, (int) $validated['quantity']);

        $cart->load(['items.product']);
        $summary = $this->buildSummary($cart->items);

        $response = $request->wantsJson()
            ? response()->json(['summary' => $summary])
            : redirect()->to('/cart');

        return $response->cookie($resolved['cookie']);
    }

    public function removeItem(Request $request, Product $product)
    {
        abort_if($product->discontinued, 404);

        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart'];

        $this->cartService->removeItem($cart, $product);

        $cart->load(['items.product']);
        $summary = $this->buildSummary($cart->items);

        $response = $request->wantsJson()
            ? response()->json(['summary' => $summary])
            : redirect()->to('/cart');

        return $response->cookie($resolved['cookie']);
    }

    public function clear(Request $request)
    {
        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart'];

        $this->cartService->clear($cart);

        $response = $request->wantsJson()
            ? response()->json(['summary' => $this->buildSummary(collect())])
            : redirect()->to('/cart');

        return $response->cookie($resolved['cookie']);
    }

    public function saveDraft(Request $request)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);

        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart'];

        // Verify customer is selected
        if (! $resolved['customer']) {
            abort(422, 'Silakan pilih customer terlebih dahulu.');
        }

        $customer = $resolved['customer'];

        // Save as draft SalesOrder
        $order = $this->cartService->saveAsDraft($cart, $shopper, $customer);

        // Create a new active cart for the customer so they can continue shopping
        // CartService resolve will create one automatically

        return redirect()
            ->to('/orders')
            ->with('success', 'Draft berhasil disimpan. Anda bisa melanjutkan nanti.')
            ->cookie($resolved['cookie']);
    }

    public function loadDraft(Request $request, SalesOrder $order)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);

        // Verify the draft belongs to this sales user
        abort_unless($order->status === SalesOrder::STATUS_DRAFT, 404);
        abort_unless((int) $order->sales_id === (int) $shopper->id, 404);

        // Set the customer cookie so cart page shows the right customer
        $customer = Customer::where('id', $order->customer_id)
            ->where('sales_id', $shopper->id)
            ->first();

        if (! $customer) {
            abort(422, 'Customer tidak ditemukan.');
        }

        // Resolve the active cart for this customer
        $resolved = $this->cartService->resolve($request, $customer, (int) $shopper->id);
        $cart = $resolved['cart'];

        // Load draft items into cart and delete the draft
        $this->cartService->loadDraftToCart($order, $cart);

        return redirect()
            ->to('/cart')
            ->with('success', 'Draft berhasil dimuat ke keranjang.')
            ->cookie($resolved['cookie'])
            ->cookie(cookie(self::SALES_CUSTOMER_COOKIE, (string) $customer->id, 60 * 24 * 30));
    }

    public function checkout(Request $request)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper, 401);

        $rules = [
            'notes' => ['nullable', 'string', 'max:800'],
            'delivery_to' => ['nullable', 'string', 'max:120'],
            'delivery_phone' => ['nullable', 'string', 'max:30'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
        ];

        if ($shopper instanceof User && $shopper->isSales()) {
            $rules['address_id'] = ['required', 'integer', 'exists:customer_addresses,id'];
        } elseif ($shopper instanceof Customer) {
            $rules['address_id'] = [
                'nullable',
                'integer',
                Rule::exists('customer_addresses', 'id')->where(fn ($q) => $q->where('customer_id', $shopper->id)),
            ];
        }

        $validated = $request->validate($rules);

        $resolved = $this->resolveCart($request);
        $cart = $resolved['cart'];

        // For sales, ensure customer_id is passed to checkout
        if ($shopper instanceof User && $shopper->isSales()) {
            $resolvedCustomer = $resolved['customer'];
            if (! $resolvedCustomer) {
                abort(422, 'Silakan pilih customer terlebih dahulu.');
            }
            $validated['customer_id'] = $resolvedCustomer->id;

            // Verify customer belongs to this sales person
            $customer = Customer::query()
                ->where('id', $resolvedCustomer->id)
                ->where('sales_id', $shopper->id)
                ->first();

            if (! $customer) {
                abort(422, 'Customer tidak ditemukan atau bukan milik Anda.');
            }

            // Verify address belongs to this customer
            if (isset($validated['address_id'])) {
                $address = CustomerAddress::query()
                    ->where('id', $validated['address_id'])
                    ->where('customer_id', $customer->id)
                    ->first();

                if (! $address) {
                    abort(422, 'Alamat tidak ditemukan untuk customer tersebut.');
                }
            }
        }

        $order = $this->cartService->checkout($cart, $shopper, $validated);

        return redirect()
            ->to('/orders/'.$order->id)
            ->withCookie($resolved['cookie']);
    }

    public function customerAddresses(Request $request, Customer $customer)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);
        abort_unless((int) $customer->sales_id === (int) $shopper->id, 403);

        $addresses = CustomerAddress::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        if ($addresses->isEmpty() && trim((string) $customer->address) !== '') {
            $seeded = CustomerAddress::query()->create([
                'customer_id' => $customer->id,
                'label' => 'Alamat Utama',
                'recipient_name' => $customer->full_name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'province' => $customer->province,
                'city' => $customer->city,
                'postal_code' => $customer->postal_code,
                'is_active' => true,
            ]);

            $addresses = collect([$seeded]);
        }

        $activeAddressId = $addresses->firstWhere('is_active', true)?->id;

        return response()->json([
            'addresses' => $addresses->map(fn (CustomerAddress $addr) => [
                'id' => $addr->id,
                'label' => $addr->label,
                'recipient_name' => $addr->recipient_name,
                'phone' => $addr->phone,
                'full_address' => $addr->full_address,
                'is_active' => (bool) $addr->is_active,
            ]),
            'active_address_id' => $activeAddressId,
        ]);
    }

    public function setActiveCustomer(int $customerId)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);

        $customer = Customer::where('id', $customerId)
            ->where('sales_id', $shopper->id)
            ->first();

        if (! $customer) {
            abort(422, 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        return redirect()
            ->to('/cart')
            ->withCookie(cookie(self::SALES_CUSTOMER_COOKIE, (string) $customer->id, 60 * 24 * 30));
    }

    public function clearActiveCustomer()
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);

        return redirect()
            ->to('/cart')
            ->withCookie(cookie(self::SALES_CUSTOMER_COOKIE, '', -1));
    }

    /**
     * Return list of customers belonging to the logged-in sales person (JSON).
     */
    public function myCustomers(Request $request)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper && $shopper instanceof User && $shopper->isSales(), 401);

        $q = $request->query('q', '');
        $customers = Customer::where('sales_id', $shopper->id)
            ->when($q !== '', fn ($query) => $query->where('full_name', 'like', '%'.$q.'%'))
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'company_name']);

        return response()->json([
            'customers' => $customers->map(fn ($c) => [
                'id' => $c->id,
                'full_name' => $c->full_name,
                'company_name' => $c->company_name,
            ]),
        ]);
    }

    private function buildSummary($items): array
    {
        $totalItems = 0;
        $subtotal = 0.0;
        $itemDetails = [];

        foreach ($items as $item) {
            $qty = (int) ($item->quantity ?? 0);
            $product = $item->product;
            $pricing = $product ? $product->pricingForQuantity($qty) : null;
            $unitPrice = (float) ($pricing['unit_price'] ?? 0);
            $discountPercent = (float) ($pricing['discount_percent'] ?? 0);
            $netPrice = (float) ($pricing['net_price'] ?? 0);
            $lineTotal = $netPrice * $qty;
            $totalItems += $qty;
            $subtotal += $lineTotal;

            if ($product) {
                $itemDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'net_price' => $netPrice,
                    'line_total' => $lineTotal,
                ];
            }
        }

        return [
            'total_items' => $totalItems,
            'subtotal' => $subtotal,
            'grand_total' => $subtotal,
            'items' => $itemDetails,
        ];
    }
}
