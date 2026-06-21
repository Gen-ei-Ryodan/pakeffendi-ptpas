<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends Controller
{
    public function index(Customer $customer)
    {
        $addresses = $customer->addresses()
            ->orderByDesc('is_active')
            ->latest()
            ->get();

        return view('admin.customers.addresses', [
            'customer' => $customer,
            'addresses' => $addresses,
        ]);
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:1000'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
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
                'recipient_name' => $validated['recipient_name'] ?? $customer->full_name,
                'phone' => $validated['phone'] ?? $customer->phone,
                'address' => $validated['address'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'is_active' => $shouldActivate,
            ]);

            if ($shouldActivate) {
                $customer->update([
                    'address' => $address->address,
                    'province' => $address->province,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                ]);
            }
        });

        ActivityLogger::log('created', 'Customer Address - '.$customer->customer_code);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('status', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, Customer $customer, CustomerAddress $address)
    {
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:60'],
            'recipient_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'address' => ['required', 'string', 'max:1000'],
            'province' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
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
                'recipient_name' => $validated['recipient_name'] ?? $customer->full_name,
                'phone' => $validated['phone'] ?? $customer->phone,
                'address' => $validated['address'],
                'province' => $validated['province'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'is_active' => $shouldActivate ? true : $address->is_active,
            ]);

            if ($address->is_active) {
                $customer->update([
                    'address' => $address->address,
                    'province' => $address->province,
                    'city' => $address->city,
                    'postal_code' => $address->postal_code,
                ]);
            }
        });

        ActivityLogger::log('updated', 'Customer Address - '.$customer->customer_code);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('status', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Customer $customer, CustomerAddress $address)
    {
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        DB::transaction(function () use ($customer, $address) {
            $wasActive = (bool) $address->is_active;
            $address->delete();

            if ($wasActive) {
                $next = $customer->addresses()->orderByDesc('id')->first();
                if ($next) {
                    $customer->addresses()->where('id', $next->id)->update(['is_active' => true]);
                    $customer->update([
                        'address' => $next->address,
                        'province' => $next->province,
                        'city' => $next->city,
                        'postal_code' => $next->postal_code,
                    ]);
                } else {
                    $customer->update(['address' => null, 'province' => null, 'city' => null, 'postal_code' => null]);
                }
            }
        });

        ActivityLogger::log('deleted', 'Customer Address - '.$customer->customer_code);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('status', 'Alamat berhasil dihapus.');
    }

    public function setActive(Customer $customer, CustomerAddress $address)
    {
        abort_unless((int) $address->customer_id === (int) $customer->id, 404);

        DB::transaction(function () use ($customer, $address) {
            $customer->addresses()->update(['is_active' => false]);
            $address->update(['is_active' => true]);
            $customer->update([
                'address' => $address->address,
                'province' => $address->province,
                'city' => $address->city,
                'postal_code' => $address->postal_code,
            ]);
        });

        ActivityLogger::log('updated', 'Customer Address Active - '.$customer->customer_code);

        return redirect()->route('admin.customers.addresses.index', $customer)
            ->with('status', 'Alamat utama berhasil diubah.');
    }
}
