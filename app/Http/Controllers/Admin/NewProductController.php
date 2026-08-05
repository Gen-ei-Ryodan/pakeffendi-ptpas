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
        $products = Product::query()
            ->with('newProduct')
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
        $newProduct = $product->newProduct;

        return view('admin.new-products.edit', [
            'product' => $product,
            'newProduct' => $newProduct,
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

        $newProduct = NewProduct::query()->updateOrCreate(
            ['product_id' => $product->id],
            $validated
        );

        ActivityLogger::log('updated', 'New Product - ' . $product->name);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Urutan produk terbaru berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        NewProduct::query()->where('product_id', $product->id)->delete();

        ActivityLogger::log('deleted', 'New Product - ' . $product->name);

        return redirect()->route('admin.new-products.index')
            ->with('status', 'Produk terbaru berhasil dihapus.');
    }
}
