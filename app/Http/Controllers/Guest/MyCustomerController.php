<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\SalesOrder;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $customer->loadMissing('addresses');
        
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

    // ===================== ADDRESS MANAGEMENT (SALES) =====================

    private function verifyOwnership(Customer $customer): \Illuminate\Foundation\Auth\User
    {
        $sales = Auth::guard('web')->user();
        abort_unless($sales && $sales->isSales(), 403);
        abort_unless((int) $customer->sales_id === (int) $sales->id, 403);
        return $sales;
    }

    public function addresses(Customer $customer)
    {
        $this->verifyOwnership($customer);

        $addresses = CustomerAddress::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'addresses' => $addresses->map(fn (CustomerAddress $a) => [
                'id' => $a->id,
                'label' => $a->label,
                'recipient_name' => $a->recipient_name,
                'phone' => $a->phone,
                'address' => $a->address,
                'province_code' => $a->province_code,
                'province_name' => $a->province_name,
                'regency_code' => $a->regency_code,
                'regency_name' => $a->regency_name,
                'district_code' => $a->district_code,
                'district_name' => $a->district_name,
                'village_code' => $a->village_code,
                'village_name' => $a->village_name,
                'postal_code' => $a->postal_code,
                'full_address' => $a->full_address,
                'is_active' => (bool) $a->is_active,
            ]),
        ]);
    }

    public function storeAddress(Request $request, Customer $customer)
    {
        $sales = $this->verifyOwnership($customer);

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:1000'],
            'province_code' => ['required', 'string', 'max:16'],
            'province_name' => ['required', 'string', 'max:120'],
            'regency_code' => ['required', 'string', 'max:16'],
            'regency_name' => ['required', 'string', 'max:120'],
            'district_code' => ['required', 'string', 'max:16'],
            'district_name' => ['required', 'string', 'max:120'],
            'village_code' => ['required', 'string', 'max:16'],
            'village_name' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $shouldActivate = (bool) ($validated['is_active'] ?? false);
        if ($customer->addresses()->count() === 0) {
            $shouldActivate = true;
        }

        DB::transaction(function () use ($customer, $validated, $shouldActivate) {
            if ($shouldActivate) {
                $customer->addresses()->update(['is_active' => false]);
            }

            $address = $customer->addresses()->create([
                'label' => $validated['label'] ?? null,
                'recipient_name' => $validated['recipient_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'],
                'province_code' => $validated['province_code'],
                'province_name' => $validated['province_name'],
                'regency_code' => $validated['regency_code'],
                'regency_name' => $validated['regency_name'],
                'district_code' => $validated['district_code'],
                'district_name' => $validated['district_name'],
                'village_code' => $validated['village_code'],
                'village_name' => $validated['village_name'],
                'province' => $validated['province_name'],
                'city' => $validated['regency_name'],
                'postal_code' => $validated['postal_code'] ?? null,
                'is_active' => $shouldActivate,
            ]);

            if ($shouldActivate) {
                $customer->update([
                    'address' => $address->address,
                    'province' => $address->province_name,
                    'city' => $address->regency_name,
                    'postal_code' => $address->postal_code,
                ]);
            }
        });

        ActivityLogger::log('created', 'Address added for customer '.$customer->full_name.' by Sales '.$sales->name);

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('status', 'Alamat berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, Customer $customer, CustomerAddress $address)
    {
        $sales = $this->verifyOwnership($customer);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:1000'],
            'province_code' => ['required', 'string', 'max:16'],
            'province_name' => ['required', 'string', 'max:120'],
            'regency_code' => ['required', 'string', 'max:16'],
            'regency_name' => ['required', 'string', 'max:120'],
            'district_code' => ['required', 'string', 'max:16'],
            'district_name' => ['required', 'string', 'max:120'],
            'village_code' => ['required', 'string', 'max:16'],
            'village_name' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $shouldActivate = (bool) ($validated['is_active'] ?? false);

        DB::transaction(function () use ($customer, $address, $validated, $shouldActivate) {
            if ($shouldActivate) {
                $customer->addresses()->where('id', '!=', $address->id)->update(['is_active' => false]);
            }

            $address->update([
                'label' => $validated['label'] ?? null,
                'recipient_name' => $validated['recipient_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'],
                'province_code' => $validated['province_code'],
                'province_name' => $validated['province_name'],
                'regency_code' => $validated['regency_code'],
                'regency_name' => $validated['regency_name'],
                'district_code' => $validated['district_code'],
                'district_name' => $validated['district_name'],
                'village_code' => $validated['village_code'],
                'village_name' => $validated['village_name'],
                'province' => $validated['province_name'],
                'city' => $validated['regency_name'],
                'postal_code' => $validated['postal_code'] ?? null,
                'is_active' => $shouldActivate ? true : $address->is_active,
            ]);

            if ($address->is_active) {
                $customer->update([
                    'address' => $address->address,
                    'province' => $address->province_name,
                    'city' => $address->regency_name,
                    'postal_code' => $address->postal_code,
                ]);
            }
        });

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('status', 'Alamat berhasil diperbarui.');
    }

    public function destroyAddress(Customer $customer, CustomerAddress $address)
    {
        $sales = $this->verifyOwnership($customer);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        DB::transaction(function () use ($customer, $address) {
            $wasActive = (bool) $address->is_active;
            $address->delete();

            if (! $wasActive) {
                return;
            }

            $next = $customer->addresses()->orderByDesc('id')->first();
            if (! $next) {
                $customer->update([
                    'address' => null,
                    'province' => null,
                    'city' => null,
                    'postal_code' => null,
                ]);
                return;
            }

            $customer->addresses()->where('id', $next->id)->update(['is_active' => true]);
            $customer->update([
                'address' => $next->address,
                'province' => $next->province_name,
                'city' => $next->regency_name,
                'postal_code' => $next->postal_code,
            ]);
        });

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('status', 'Alamat berhasil dihapus.');
    }

    public function setActiveAddress(Customer $customer, CustomerAddress $address)
    {
        $this->verifyOwnership($customer);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        DB::transaction(function () use ($customer, $address) {
            $customer->addresses()->update(['is_active' => false]);
            $address->update(['is_active' => true]);
            $customer->update([
                'address' => $address->address,
                'province' => $address->province_name,
                'city' => $address->regency_name,
                'postal_code' => $address->postal_code,
            ]);
        });

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('status', 'Alamat aktif berhasil diubah.');
    }
}
