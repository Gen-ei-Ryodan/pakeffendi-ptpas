<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GuestOrderApiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer.full_name' => ['required', 'string', 'max:120'],
            'customer.email' => ['required', 'email', 'max:190'],
            'customer.phone' => ['required', 'string', 'max:30'],
            'customer.address' => ['nullable', 'string', 'max:500'],
            'delivery_to' => ['nullable', 'string', 'max:120'],
            'delivery_phone' => ['nullable', 'string', 'max:30'],
            'delivery_address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:800'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        return DB::transaction(function () use ($validated) {
            $customer = Customer::query()->firstOrCreate(
                ['email' => $validated['customer']['email']],
                [
                    'customer_code' => $this->generateCustomerCode(),
                    'full_name' => $validated['customer']['full_name'],
                    'account_type' => 'Guest',
                    'ktp_number' => $this->generateKtpNumber(),
                    'npwp' => null,
                    'password' => Hash::make(Str::random(24)),
                    'address' => $validated['customer']['address'] ?? null,
                    'province' => null,
                    'city' => null,
                    'postal_code' => null,
                    'phone' => $validated['customer']['phone'],
                    'contact_person' => $validated['customer']['full_name'],
                    'company_name' => null,
                    'internal_code' => null,
                ]
            );

            $itemsInput = $validated['items'];
            $productIds = collect($itemsInput)->pluck('product_id')->unique()->values()->all();
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->where('discontinued', false)
                ->whereHas('category', fn ($q) => $q->where('is_active', true))
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($productIds)) {
                abort(422, 'Ada produk yang tidak tersedia.');
            }

            $order = SalesOrder::createWithNextOrderNo([
                'order_date' => now(),
                'customer_id' => $customer->id,
                'payment_type' => null,
                'status' => 'Payment Pending',
                'sales_person_id' => null,
                'shipping_fee' => 0,
                'grand_total' => 0,
                'dpp' => 0,
                'ppn' => 0,
                'ppn_percent' => 0,
                'process_date' => null,
                'process_time' => null,
                'process_order_no' => null,
                'notes' => $validated['notes'] ?? null,
                'delivery_to' => $validated['delivery_to'] ?? $customer->full_name,
                'delivery_address' => $validated['delivery_address'] ?? ($customer->address ?? null),
                'delivery_phone' => $validated['delivery_phone'] ?? $customer->phone,
            ]);

            $grandTotal = 0;

            foreach ($itemsInput as $itemInput) {
                $product = $products->get($itemInput['product_id']);
                $qty = (int) $itemInput['quantity'];

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

            return response()->json([
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'status' => $order->status,
                'grand_total' => (float) $order->grand_total,
            ], 201);
        });
    }

    private function generateCustomerCode(): string
    {
        return 'W'.now()->format('ym').str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function generateKtpNumber(): string
    {
        return (string) random_int(1000000000000000, 9999999999999999);
    }
}
