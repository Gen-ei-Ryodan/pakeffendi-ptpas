<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerChangeRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', ''); // Filter by status

        $customers = Customer::query()
            ->with('sales:id,name') // Eager load sales name
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('customer_code', 'like', "%{$q}%")
                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('city', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('created_at') // Show newest first
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'q' => $q,
            'status' => $status,
        ]);
    }
    
    public function approve(Customer $customer)
    {
        $customer->update(['status' => 'active']);
        
        // Generate customer code if missing
        if (empty($customer->customer_code)) {
            $customer->update(['customer_code' => $this->generateCustomerCode()]);
        }

        ActivityLogger::log('approved', 'Customer Approved - '.$customer->full_name);

        return redirect()->back()->with('status', 'Customer berhasil disetujui.');
    }

    public function reject(Customer $customer)
    {
        $customer->update(['status' => 'rejected']);
        
        ActivityLogger::log('rejected', 'Customer Rejected - '.$customer->full_name);

        return redirect()->back()->with('status', 'Customer ditolak.');
    }

    public function show(Customer $customer)
    {
        $customer->load(['sales:id,name', 'addresses']);
        $pendingChanges = CustomerChangeRequest::query()
            ->where('customer_id', $customer->id)
            ->where('status', 'pending')
            ->with('sales:id,name')
            ->latest()
            ->first();

        return view('admin.customers.show', [
            'customer' => $customer,
            'pendingChanges' => $pendingChanges,
        ]);
    }

    public function create()
    {
        $sales = \App\Models\User::where('role', 'sales')->orderBy('name')->get();
        return view('admin.customers.create', compact('sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', 'max:50'],
            'ktp_number' => ['required', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'store_photo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['required', 'string', 'max:30', 'unique:customers,phone'],
            'contact_person' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'internal_code' => ['nullable', 'string', 'max:100'],
            'sales_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->hasFile('store_photo')) {
            $validated['store_photo_path'] = $request->file('store_photo')->store('customer-photos', 'public');
        }
        unset($validated['store_photo']);

        $validated['customer_code'] = $this->generateCustomerCode();
        $validated['password'] = Hash::make($validated['password']);

        $customer = Customer::create($validated);

        ActivityLogger::log('created', 'Customer - '.$customer->customer_code);

        return redirect()->route('admin.customers.index')->with('status', 'Customer berhasil dibuat.');
    }

    public function edit(Customer $customer)
    {
        $sales = \App\Models\User::where('role', 'sales')->orderBy('name')->get();
        return view('admin.customers.edit', [
            'customer' => $customer,
            'sales' => $sales,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', 'string', 'max:50'],
            'ktp_number' => ['required', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($customer->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string'],
            'province' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'google_maps_url' => ['nullable', 'string', 'max:500'],
            'store_photo' => ['nullable', 'image', 'max:2048'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('customers', 'phone')->ignore($customer->id)],
            'contact_person' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'internal_code' => ['nullable', 'string', 'max:100'],
            'sales_id' => ['nullable', 'exists:users,id'],
        ]);

        if ($request->hasFile('store_photo')) {
            // Delete old photo
            if ($customer->store_photo_path) {
                Storage::disk('public')->delete($customer->store_photo_path);
            }
            $validated['store_photo_path'] = $request->file('store_photo')->store('customer-photos', 'public');
        }
        unset($validated['store_photo']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $customer->update($validated);

        ActivityLogger::log('updated', 'Customer - '.$customer->customer_code);

        return redirect()->route('admin.customers.index')->with('status', 'Customer berhasil diupdate.');
    }

    public function destroy(Customer $customer)
    {
        $code = $customer->customer_code;
        
        // Clean up store photo
        if ($customer->store_photo_path) {
            Storage::disk('public')->delete($customer->store_photo_path);
        }
        
        $customer->delete();

        ActivityLogger::log('deleted', 'Customer - '.$code);

        return redirect()->route('admin.customers.index')->with('status', 'Customer berhasil dihapus.');
    }

    // ========== Customer Change Request Approval ==========

    public function changeRequestIndex(Request $request)
    {
        $changeRequests = CustomerChangeRequest::query()
            ->with(['customer', 'sales:id,name'])
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.change-requests', [
            'changeRequests' => $changeRequests,
            'status' => $request->query('status'),
        ]);
    }

    public function approveChangeRequest(CustomerChangeRequest $changeRequest)
    {
        if ($changeRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan perubahan sudah diproses sebelumnya.');
        }

        $admin = auth()->guard('web')->user();

        DB::transaction(function () use ($changeRequest, $admin) {
            $customer = $changeRequest->customer;

            // Apply changes to customer
            $changes = $changeRequest->changes;
            $customer->update($changes);

            $changeRequest->update([
                'status' => 'approved',
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ]);
        });

        ActivityLogger::log('approved', 'Customer change request approved for '.$changeRequest->customer->full_name);

        return redirect()->back()->with('status', 'Perubahan data customer telah disetujui dan diterapkan.');
    }

    public function rejectChangeRequest(Request $request, CustomerChangeRequest $changeRequest)
    {
        if ($changeRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan perubahan sudah diproses sebelumnya.');
        }

        $reason = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ])['rejection_reason'] ?? null;

        $changeRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        ActivityLogger::log('rejected', 'Customer change request rejected for '.$changeRequest->customer->full_name);

        return redirect()->back()->with('status', 'Permintaan perubahan data customer ditolak.');
    }

    public function showChangeRequest(CustomerChangeRequest $changeRequest)
    {
        $changeRequest->load(['customer', 'sales:id,name', 'approver:id,name']);

        return view('admin.customers.change-request-show', [
            'changeRequest' => $changeRequest,
            'fieldCount' => count($changeRequest->changes ?? []),
        ]);
    }

    private function generateCustomerCode(): string
    {
        do {
            $code = 'C'.strtoupper(Str::random(8));
        } while (Customer::query()->where('customer_code', $code)->exists());

        return $code;
    }
}
