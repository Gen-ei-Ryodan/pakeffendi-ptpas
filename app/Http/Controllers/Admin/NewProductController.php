<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class NewProductController extends Controller
{
    private const STATUS = 'TERBARU';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.new-products.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('admin.new-products.edit', [
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $product->update([
            'status_product' => self::STATUS,
            'no_urut_status' => (int) $validated['sort_order'],
        ]);

        ActivityLogger::log('updated', 'New Product (Terbaru) - ' . $product->name);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Urutan produk terbaru berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        $product->update([
            'status_product' => null,
            'no_urut_status' => 0,
        ]);

        ActivityLogger::log('deleted', 'New Product (Terbaru) - ' . $productName);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Produk terbaru berhasil dihapus.');
    }
}