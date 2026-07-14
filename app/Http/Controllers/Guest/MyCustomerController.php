<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerChangeRequest;
use App\Models\SalesOrder;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MyCustomerController extends Controller
{
    public function index(Request $request)
    {
        $sales = Auth::guard('web')->user();

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
            'customer' => $sales,
        ]);
    }

    public function show(Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

        if ((int) $customer->sales_id !== (int) $sales->id) {
            return redirect()
                ->route('guest.profile.my-customers.index')
                ->with('error', 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        $customer->loadCount('salesOrders');
        $customer->load('addresses');

        $orders = SalesOrder::query()
            ->where('customer_id', $customer->id)
            ->latest('order_date')
            ->paginate(10);

        // Load pending change requests
        $pendingChanges = CustomerChangeRequest::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('guest.profile.my-customers.show', [
            'myCustomer' => $customer,
            'orders' => $orders,
            'is_sales' => true,
            'customer' => $sales,
            'pendingChanges' => $pendingChanges,
        ]);
    }

    public function edit(Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

        if ((int) $customer->sales_id !== (int) $sales->id) {
            return redirect()
                ->route('guest.profile.my-customers.index')
                ->with('error', 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        return view('guest.profile.my-customers.edit', [
            'myCustomer' => $customer,
            'is_sales' => true,
            'customer' => $sales,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

        if ((int) $customer->sales_id !== (int) $sales->id) {
            return redirect()
                ->route('guest.profile.my-customers.index')
                ->with('error', 'Customer tidak ditemukan atau bukan milik Anda.');
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'address' => ['nullable', 'string', 'max:1000'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'ktp_number' => ['nullable', 'string', 'max:50'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'store_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // Handle store photo upload
        if ($request->hasFile('store_photo')) {
            $path = $request->file('store_photo')->store('customer-photos', 'public');
            $validated['store_photo_path'] = $path;
        }
        unset($validated['store_photo']);

        // Build changes array (only changed fields)
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($key === 'password' && !empty($value)) {
                $changes[$key] = bcrypt($value);
            } elseif ($key !== 'password') {
                $currentValue = (string) ($customer->$key ?? '');
                if ((string) $value !== $currentValue) {
                    $changes[$key] = $value;
                }
            }
        }

        // If password is empty, don't include it
        if (empty($validated['password'])) {
            unset($changes['password']);
        }

        if (empty($changes)) {
            return redirect()
                ->route('guest.profile.my-customers.show', $customer)
                ->with('info', 'Tidak ada perubahan yang dilakukan.');
        }

        // Reject if there's already a pending change request
        $existingPending = CustomerChangeRequest::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            return redirect()
                ->route('guest.profile.my-customers.show', $customer)
                ->with('error', 'Masih ada permintaan perubahan yang menunggu approval admin. Harap tunggu hingga disetujui.');
        }

        CustomerChangeRequest::create([
            'customer_id' => $customer->id,
            'sales_id' => $sales->id,
            'changes' => $changes,
            'status' => 'pending',
        ]);

        ActivityLogger::log('created', 'Customer change request for '.$customer->full_name.' by Sales '.$sales->name);

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('status', 'Permintaan perubahan data customer telah dikirim untuk approval admin.');
    }

    public function destroy(Customer $customer)
    {
        $sales = Auth::guard('web')->user();

        if (!$sales || !$sales->isSales()) {
            abort(403);
        }

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
