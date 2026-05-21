<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductStatus;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ProductStatusController extends Controller
{
    public function index()
    {
        $statuses = ProductStatus::query()->orderBy('sort_order')->get();
        return view('admin.statuses.index', compact('statuses'));
    }

    public function create()
    {
        return view('admin.statuses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:product_statuses,code'],
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        ProductStatus::create($validated);

        ActivityLogger::log('created', 'ProductStatus - '.$validated['code']);

        return redirect()->route('admin.statuses.index')->with('status', 'Status berhasil dibuat.');
    }

    public function edit(ProductStatus $productStatus)
    {
        return view('admin.statuses.edit', ['status' => $productStatus]);
    }

    public function update(Request $request, ProductStatus $productStatus)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:product_statuses,code,'.$productStatus->id],
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $productStatus->update($validated);

        ActivityLogger::log('updated', 'ProductStatus - '.$validated['code']);

        return redirect()->route('admin.statuses.index')->with('status', 'Status berhasil diupdate.');
    }

    public function destroy(ProductStatus $productStatus)
    {
        $code = $productStatus->code;
        $productStatus->delete();

        ActivityLogger::log('deleted', 'ProductStatus - '.$code);

        return redirect()->route('admin.statuses.index')->with('status', 'Status berhasil dihapus.');
    }
}
