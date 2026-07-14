<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    /**
     * Get the authenticated customer (buyer) or null.
     */
    private function getCustomer()
    {
        if (Auth::guard('customer')->check()) {
            return Auth::guard('customer')->user();
        }

        return null;
    }

    /**
     * Check if current user is a buyer (customer guard).
     * Buyers can only VIEW addresses, not create/edit/delete.
     */
    private function isBuyer(): bool
    {
        return Auth::guard('customer')->check();
    }

    public function index()
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);

        $addresses = $customer->addresses()
            ->orderByDesc('is_active')
            ->latest()
            ->get();

        return view('guest.profile.addresses.index', [
            'customer' => $customer,
            'is_sales' => false,
            'addresses' => $addresses,
            'readonly' => $this->isBuyer(), // Buyer only views, cannot edit
        ]);
    }

    public function store(Request $request)
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);

        // Buyers cannot add their own address
        if ($this->isBuyer()) {
            return redirect()->route('guest.profile.addresses.index')
                ->with('error', 'Anda tidak dapat menambah alamat sendiri. Silakan hubungi sales Anda.');
        }

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

        return redirect()->route('guest.profile.addresses.index')->with('status', 'Alamat berhasil ditambahkan.');
    }

    public function edit(CustomerAddress $address)
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        // Buyers cannot edit their own address
        if ($this->isBuyer()) {
            return redirect()->route('guest.profile.addresses.index')
                ->with('error', 'Anda tidak dapat mengubah alamat sendiri. Silakan hubungi sales Anda.');
        }

        return view('guest.profile.addresses.edit', [
            'customer' => $customer,
            'is_sales' => false,
            'address' => $address,
        ]);
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        // Buyers cannot update their own address
        if ($this->isBuyer()) {
            return redirect()->route('guest.profile.addresses.index')
                ->with('error', 'Anda tidak dapat mengubah alamat sendiri. Silakan hubungi sales Anda.');
        }

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

        return redirect()->route('guest.profile.addresses.index')->with('status', 'Alamat berhasil diperbarui.');
    }

    public function destroy(CustomerAddress $address)
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        // Buyers cannot delete their own address
        if ($this->isBuyer()) {
            return redirect()->route('guest.profile.addresses.index')
                ->with('error', 'Anda tidak dapat menghapus alamat sendiri. Silakan hubungi sales Anda.');
        }

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

        return redirect()->route('guest.profile.addresses.index')->with('status', 'Alamat berhasil dihapus.');
    }

    public function setActive(CustomerAddress $address)
    {
        $customer = $this->getCustomer();
        abort_unless($customer, 401);
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        // Buyers cannot change active address
        if ($this->isBuyer()) {
            return redirect()->route('guest.profile.addresses.index')
                ->with('error', 'Anda tidak dapat mengubah alamat aktif. Silakan hubungi sales Anda.');
        }

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

        return redirect()->route('guest.profile.addresses.index')->with('status', 'Alamat aktif berhasil diubah.');
    }
}
