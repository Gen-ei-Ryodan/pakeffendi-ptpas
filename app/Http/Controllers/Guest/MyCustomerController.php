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

    /**
     * Verify that the authenticated user owns this customer.
     */
    private function verifyOwnership(Customer $customer): \App\Models\User
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
        $this->verifyOwnership($customer);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        return redirect()
            ->route('guest.profile.my-customers.show', $customer)
            ->with('error', 'Anda tidak dapat menghapus alamat. Silakan hubungi admin.');
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
