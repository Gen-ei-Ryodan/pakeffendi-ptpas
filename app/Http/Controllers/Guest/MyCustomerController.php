<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyCustomerController extends Controller
{
    public function index(Request $request)
    {
        $sales = Auth::guard('web')->user();

        // Ensure user is Sales
        if (!$sales || !$sales->isSales()) {
            return redirect()->route('guest.profile.index');
        }

        $q = $request->query('q');

        $customers = Customer::query()
            ->where('sales_id', $sales->id)
            ->withCount('salesOrders as total_orders')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qBuilder) use ($q) {
                    $qBuilder->where('full_name', 'like', "%{$q}%")
                        ->orWhere('customer_code', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('total_orders')
            ->orderBy('full_name')
            ->paginate(10)
            ->withQueryString();

        return view('guest.profile.my-customers.index', [
            'customers' => $customers,
            'q' => $q,
            'is_sales' => true,
            'customer' => $sales, // For layout compatibility
        ]);
    }

    public function show(Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

        // Redirect to index if sales doesn't own this customer
        if ((int) $customer->sales_id !== (int) $sales->id) {
            return redirect()
                ->route('guest.profile.my-customers.index')
                ->with('error', 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        $customer->loadCount('salesOrders');
        
        $orders = SalesOrder::query()
            ->where('customer_id', $customer->id)
            ->latest('order_date')
            ->paginate(10);

        return view('guest.profile.my-customers.show', [
            'myCustomer' => $customer, // Rename to avoid conflict with layout 'customer'
            'orders' => $orders,
            'is_sales' => true,
            'customer' => $sales, // For layout compatibility
        ]);
    }

    public function destroy(Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

        // Ensure sales only deletes their own customer
        if ((int) $customer->sales_id !== (int) $sales->id) {
            return redirect()
                ->route('guest.profile.my-customers.index')
                ->with('error', 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        $customerName = $customer->full_name;
        $customer->delete();

        ActivityLogger::log('deleted', 'Customer deleted by Sales - '.$customerName);

        return redirect()
            ->route('guest.profile.my-customers.index')
            ->with('success', 'Customer '.$customerName.' berhasil dihapus.');
    }
}
