<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\ActivityLogger;
use App\Services\RemoteStockService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesOrderController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $orders = SalesOrder::query()
            ->with('customer')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('order_no', 'like', "%{$q}%")
                        ->orWhere('payment_type', 'like', "%{$q}%")
                        ->orWhere('status', 'like', "%{$q}%");
                })->orWhereHas('customer', function ($query) use ($q) {
                    $query
                        ->where('customer_code', 'like', "%{$q}%")
                        ->orWhere('full_name', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.sales-orders.index', [
            'orders' => $orders,
            'q' => $q,
        ]);
    }

    public function show(SalesOrder $salesOrder, RemoteStockService $remoteStock)
    {
        $order = $salesOrder->load(['customer', 'items.product']);

        $skus = $order->items->pluck('product.sku')->filter()->values()->toArray();
        $stockMap = $remoteStock->getStockBatch($skus);

        return view('admin.sales-orders.show', [
            'order' => $order,
            'stockMap' => $stockMap,
        ]);
    }

    public function edit(SalesOrder $salesOrder)
    {
        return view('admin.sales-orders.edit', [
            'order' => $salesOrder,
            'statuses' => [
                SalesOrder::STATUS_NEW,
                SalesOrder::STATUS_ON_PROGRESS,
                SalesOrder::STATUS_ON_DELIVERY,
                SalesOrder::STATUS_FINISHED,
            ],
        ]);
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->status === SalesOrder::STATUS_FINISHED) {
            return back()->withErrors(['Order sudah selesai dan tidak bisa diubah.']);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in([
                SalesOrder::STATUS_NEW,
                SalesOrder::STATUS_ON_PROGRESS,
                SalesOrder::STATUS_ON_DELIVERY,
                SalesOrder::STATUS_FINISHED,
            ])],
        ]);

        $salesOrder->status = $validated['status'];
        $salesOrder->save();

        ActivityLogger::log('updated', 'SalesOrder - '.$salesOrder->order_no);

        return redirect()->route('admin.sales-orders.show', $salesOrder)->with('status', 'Status order berhasil diupdate.');
    }

    public function destroy(SalesOrder $salesOrder)
    {
        $no = $salesOrder->order_no;
        $salesOrder->delete();

        ActivityLogger::log('deleted', 'SalesOrder - '.$no);

        return redirect()->route('admin.sales-orders.index')->with('status', 'Sales order berhasil dihapus.');
    }
}
