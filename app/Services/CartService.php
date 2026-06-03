<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    public const COOKIE_NAME = 'pas_cart_sid';

    /**
     * @param  Customer|null  $customer  The customer the cart belongs to (null for guest/session cart).
     * @param  int|null  $salesId  ID of the sales person creating the cart on behalf of the customer.
     */
    public function resolve(Request $request, $customer = null, ?int $salesId = null): array
    {
        if ($customer instanceof Customer) {
            $cart = Cart::query()
                ->where('customer_id', $customer->id)
                ->where('status', 'active')
                ->latest('id')
                ->first();

            if (! $cart) {
                $cartData = [
                    'customer_id' => $customer->id,
                    'session_id' => (string) Str::uuid(),
                    'status' => 'active',
                ];
                if ($salesId !== null) {
                    $cartData['sales_id'] = $salesId;
                }
                $cart = Cart::query()->create($cartData);
            } elseif ($salesId !== null && $cart->sales_id === null) {
                $cart->update(['sales_id' => $salesId]);
            }

            $guestSessionId = (string) $request->cookie(self::COOKIE_NAME, '');
            if ($guestSessionId !== '' && $guestSessionId !== $cart->session_id) {
                $guestCart = Cart::query()
                    ->whereNull('customer_id')
                    ->where('session_id', $guestSessionId)
                    ->where('status', 'active')
                    ->latest('id')
                    ->first();

                if ($guestCart && $guestCart->items()->exists()) {
                    $this->merge($guestCart, $cart);
                }
            }

            return [
                'cart' => $cart,
                'cookie' => cookie(self::COOKIE_NAME, $cart->session_id, 60 * 24 * 30),
            ];
        }

        $sessionId = (string) $request->cookie(self::COOKIE_NAME, '');
        if ($sessionId === '') {
            $sessionId = (string) Str::uuid();
        } else {
            $existing = Cart::query()
                ->where('session_id', $sessionId)
                ->latest('id')
                ->first();

            if ($existing && ($existing->customer_id !== null || $existing->status !== 'active')) {
                $sessionId = (string) Str::uuid();
            }
        }

        $cart = Cart::query()
            ->whereNull('customer_id')
            ->where('session_id', $sessionId)
            ->where('status', 'active')
            ->latest('id')
            ->first();

        if (! $cart) {
            try {
                $cart = Cart::query()->create([
                    'customer_id' => null,
                    'session_id' => $sessionId,
                    'status' => 'active',
                ]);
            } catch (QueryException $e) {
                $code = (string) ($e->errorInfo[1] ?? '');
                if ($e->getCode() !== '23000' && $code !== '1062') {
                    throw $e;
                }

                $existing = Cart::query()
                    ->where('session_id', $sessionId)
                    ->latest('id')
                    ->first();

                if ($existing && $existing->customer_id === null && $existing->status === 'active') {
                    $cart = $existing;
                } else {
                    $sessionId = (string) Str::uuid();
                    $cart = Cart::query()->create([
                        'customer_id' => null,
                        'session_id' => $sessionId,
                        'status' => 'active',
                    ]);
                }
            }
        }

        return [
            'cart' => $cart,
            'cookie' => cookie(self::COOKIE_NAME, $sessionId, 60 * 24 * 30),
        ];
    }

    public function addItem(Cart $cart, Product $product, int $quantity): CartItem
    {
        $quantity = max(1, $quantity);

        return DB::transaction(function () use ($cart, $product, $quantity) {
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (! $item) {
                return CartItem::query()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }

            $item->update([
                'quantity' => $item->quantity + $quantity,
            ]);

            return $item->refresh();
        });
    }

    public function setItemQuantity(Cart $cart, Product $product, int $quantity): void
    {
        $quantity = (int) $quantity;

        DB::transaction(function () use ($cart, $product, $quantity) {
            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            if (! $item) {
                if ($quantity <= 0) {
                    return;
                }

                CartItem::query()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);

                return;
            }

            if ($quantity <= 0) {
                $item->delete();

                return;
            }

            $item->update(['quantity' => $quantity]);
        });
    }

    public function removeItem(Cart $cart, Product $product): void
    {
        CartItem::query()
            ->where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->delete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function merge(Cart $source, Cart $target): void
    {
        DB::transaction(function () use ($source, $target) {
            $sourceItems = $source->items()->lockForUpdate()->get();

            foreach ($sourceItems as $sourceItem) {
                $targetItem = CartItem::query()
                    ->where('cart_id', $target->id)
                    ->where('product_id', $sourceItem->product_id)
                    ->lockForUpdate()
                    ->first();

                if ($targetItem) {
                    $targetItem->update([
                        'quantity' => $targetItem->quantity + $sourceItem->quantity,
                    ]);
                } else {
                    CartItem::query()->create([
                        'cart_id' => $target->id,
                        'product_id' => $sourceItem->product_id,
                        'quantity' => $sourceItem->quantity,
                    ]);
                }
            }

            $source->items()->delete();
            $source->update(['status' => 'converted']);
        });
    }

    /**
     * @param  Customer|User  $shopper
     */
    public function checkout(Cart $cart, $shopper, array $payload = []): SalesOrder
    {
        return DB::transaction(function () use ($cart, $shopper, $payload) {
            $items = $cart->items()->with('product')->lockForUpdate()->get();

            if ($items->isEmpty()) {
                // If cart items are empty, try to reload from database just in case
                $cart->refresh();
                $items = $cart->items()->with('product')->lockForUpdate()->get();

                if ($items->isEmpty()) {
                    throw new \Exception('Keranjang belanja kosong.');
                }
            }

            $customerId = null;
            $salesId = null;
            $deliveryTo = $payload['delivery_to'] ?? null;
            $deliveryAddress = $payload['delivery_address'] ?? null;
            $deliveryPhone = $payload['delivery_phone'] ?? null;

            if ($shopper instanceof Customer) {
                $customerId = $shopper->id;
                $salesId = $shopper->sales_id;

                $selectedAddressId = isset($payload['address_id']) ? (int) $payload['address_id'] : null;
                $activeAddress = null;

                if ($selectedAddressId) {
                    $activeAddress = CustomerAddress::query()
                        ->where('customer_id', $shopper->id)
                        ->where('id', $selectedAddressId)
                        ->first();
                }

                if (! $activeAddress) {
                    $shopper->loadMissing('activeAddress');
                    $activeAddress = $shopper->activeAddress;
                }

                $deliveryTo = $deliveryTo ?? ($activeAddress?->recipient_name ?: $shopper->full_name);
                $deliveryAddress = $deliveryAddress ?? ($activeAddress?->full_address ?: $shopper->address);
                $deliveryPhone = $deliveryPhone ?? ($activeAddress?->phone ?: $shopper->phone);
            } elseif ($shopper instanceof User && $shopper->isSales()) {
                $customerId = $payload['customer_id'] ?? null;
                if (! $customerId) {
                    abort(422, 'Customer harus dipilih.');
                }

                // Verify customer belongs to sales
                $targetCustomer = Customer::where('id', $customerId)
                    ->where('sales_id', $shopper->id)
                    ->first();

                if (! $targetCustomer) {
                    abort(422, 'Customer tidak ditemukan atau bukan milik Anda.');
                }

                $salesId = $shopper->id;

                $selectedAddressId = isset($payload['address_id']) ? (int) $payload['address_id'] : null;
                $activeAddress = null;

                if ($selectedAddressId) {
                    $activeAddress = CustomerAddress::query()
                        ->where('customer_id', $targetCustomer->id)
                        ->where('id', $selectedAddressId)
                        ->first();
                }

                if (! $activeAddress) {
                    $targetCustomer->loadMissing('activeAddress');
                    $activeAddress = $targetCustomer->activeAddress;
                }

                $deliveryTo = $deliveryTo ?? ($activeAddress?->recipient_name ?: $targetCustomer->full_name);
                $deliveryAddress = $deliveryAddress ?? ($activeAddress?->full_address ?: $targetCustomer->address);
                $deliveryPhone = $deliveryPhone ?? ($activeAddress?->phone ?: $targetCustomer->phone);

                // Set customer ID for the order
                $customerId = $targetCustomer->id;

            } else {
                abort(403, 'Unauthorized checkout.');
            }

            $order = SalesOrder::createWithNextOrderNo([
                'order_date' => now(),
                'customer_id' => $customerId,
                'payment_type' => 'Transfer', // Default for now
                'status' => SalesOrder::STATUS_NEW,
                'sales_person_id' => $salesId,
                'sales_id' => $salesId,
                'shipping_fee' => 0,
                'grand_total' => 0,
                'dpp' => 0,
                'ppn' => 0,
                'ppn_percent' => 11, // PPN 11%
                'process_date' => null,
                'process_time' => null,
                'process_order_no' => null,
                'notes' => $payload['notes'] ?? null,
                'delivery_to' => $deliveryTo,
                'delivery_address' => $deliveryAddress,
                'delivery_phone' => $deliveryPhone,
            ]);

            $grandTotal = 0.0;

            foreach ($items as $item) {
                $product = $item->product;

                if (! $product || $product->discontinued) {
                    abort(422, 'Ada produk yang tidak tersedia.');
                }

                $qty = (int) $item->quantity;
                $pricing = $product->pricingForQuantity($qty);
                $unitPrice = (float) $pricing['unit_price'];
                $discountPercent = (float) $pricing['discount_percent'];
                $netPrice = (float) $pricing['net_price'];
                $finalTotal = $netPrice * $qty;

                SalesOrderItem::query()->create([
                    'sales_order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'net_price' => $netPrice,
                    'discount_percent' => $discountPercent,
                    'final_total' => $finalTotal,
                ]);

                $grandTotal += $finalTotal;
            }

            $order->update([
                'grand_total' => $grandTotal,
                'dpp' => $grandTotal,
            ]);

            $cart->items()->delete();
            $cart->update(['status' => 'converted']);

            if ($shopper instanceof Customer) {
                Cart::query()->create([
                    'customer_id' => $shopper->id,
                    'session_id' => (string) Str::uuid(),
                    'status' => 'active',
                ]);
            }

            return $order;
        });
    }
}
