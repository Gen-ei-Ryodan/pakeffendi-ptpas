<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\RemoteStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
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

    public function index(Request $request)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper, 401);

        $isSales = $shopper instanceof User && $shopper->isSales();

        foreach (['status', 'customer', 'date_from', 'date_to', 'q'] as $key) {
            if ($request->has($key) && $request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:40'],
            'customer' => ['nullable', 'string', 'max:120'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $dateFrom = trim((string) ($validated['date_from'] ?? ''));
        $dateTo = trim((string) ($validated['date_to'] ?? ''));

        if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        $customerName = trim((string) ($validated['customer'] ?? ''));
        $q = trim((string) ($validated['q'] ?? ''));

        $query = SalesOrder::query()->with('customer');

        if ($shopper instanceof Customer) {
            $query->where('customer_id', $shopper->id);
        } else {
            // Sales User sees all orders assigned to them
            $query->where(function ($q2) use ($shopper) {
                $q2
                    ->where('sales_id', $shopper->id)
                    ->orWhere('sales_person_id', $shopper->id);
            });
        }

        $query
            ->when($dateFrom !== '', fn ($q2) => $q2->whereDate('order_date', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($q2) => $q2->whereDate('order_date', '<=', $dateTo))
            ->when($customerName !== '' && $shopper instanceof User && $shopper->isSales(), function ($q2) use ($customerName) {
                $q2->whereHas('customer', function ($customerQuery) use ($customerName) {
                    $customerQuery->where('full_name', 'like', "%{$customerName}%");
                });
            })
            ->when($q !== '', function ($q2) use ($q) {
                $q2->where(function ($where) use ($q) {
                    $where
                        ->where('order_no', 'like', "%{$q}%")
                        ->orWhereHas('items', function ($itemsQuery) use ($q) {
                            $itemsQuery->where(function ($itemWhere) use ($q) {
                                $itemWhere
                                    ->where('product_name', 'like', "%{$q}%")
                                    ->orWhereHas('product', function ($productQuery) use ($q) {
                                        $productQuery
                                            ->where('sku', 'like', "%{$q}%")
                                            ->orWhere('name', 'like', "%{$q}%");
                                    });
                            });
                        });
                });
            })
            ->when(! empty($validated['status']), fn ($q2) => $q2->where('status', $validated['status']));

        $totalNominal = (float) (clone $query)->sum('grand_total');
        $totalTransaksi = (int) (clone $query)->count();

        $orders = $query->withCount('items')
            ->latest('order_date')
            ->paginate(10)
            ->withQueryString();

        return view('guest.orders.index', [
            'customer' => $shopper, // View expects 'customer', passing shopper (Customer or User)
            'orders' => $orders,
            'order_filters' => [
                'status' => $validated['status'] ?? null,
                'customer' => $customerName,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'q' => $q,
            ],
            'order_stats' => [
                'total_nominal' => $totalNominal,
                'total_transaksi' => $totalTransaksi,
            ],
            'is_sales' => $isSales,
        ]);
    }

    public function show(SalesOrder $order, RemoteStockService $remoteStock)
    {
        $shopper = $this->getShopper();
        abort_unless($shopper, 401);

        if ($shopper instanceof Customer) {
            abort_unless((int) $order->customer_id === (int) $shopper->id, 404);
        } else {
            // Sales User can view if assigned to them
            abort_unless((int) $order->sales_id === (int) $shopper->id, 404);
        }

        $order->load(['items.product', 'customer']);

        $skus = $order->items->pluck('product.sku')->filter()->values()->toArray();
        $stockMap = $remoteStock->getStockBatch($skus);

        return view('guest.orders.show', [
            'customer' => $shopper,
            'order' => $order,
            'stockMap' => $stockMap,
            'is_sales' => ($shopper instanceof User && $shopper->isSales()),
        ]);
    }
}
