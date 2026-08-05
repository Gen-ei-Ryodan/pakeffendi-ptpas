<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewProduct;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class NewProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $newProducts = NewProduct::query()
            ->with('product:id,name,sku,photo_path,created_at')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.new-products.index', [
            'newProducts' => $newProducts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NewProduct $newProduct)
    {
        $newProduct->load('product:id,name,sku,created_at');

        return view('admin.new-products.edit', [
            'newProduct' => $newProduct,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewProduct $newProduct)
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $newProduct->update($validated);

        ActivityLogger::log('updated', 'New Product - ' . $newProduct->product->name);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Urutan produk terbaru berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewProduct $newProduct)
    {
        $productName = $newProduct->product->name;
        $newProduct->delete();

        ActivityLogger::log('deleted', 'New Product - ' . $productName);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Produk terbaru berhasil dihapus.');
    }
}
