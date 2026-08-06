<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class FeaturedProductController extends Controller
{
    private const STATUS = 'TERLARIS';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $featuredProducts = Product::query()
            ->whereRaw('LOWER(status_product) LIKE ?', ['%terlaris%'])
            ->orderBy('no_urut_status')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.featured-products.index', [
            'featuredProducts' => $featuredProducts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::query()
            ->whereRaw("status_product IS NULL OR status_product = '' OR LOWER(status_product) NOT LIKE ?", ['%terlaris%'])
            ->where('discontinued', false)
            ->orderBy('name')
            ->get(['id', 'name', 'sku']);

        return view('admin.featured-products.create', [
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if ($product->status_product && str_contains(strtolower($product->status_product), 'terlaris')) {
            return back()->withErrors(['product_id' => 'Produk sudah ada di daftar produk terlaris.'])->withInput();
        }

        $product->update([
            'status_product' => self::STATUS,
            'no_urut_status' => (int) $validated['sort_order'],
        ]);

        ActivityLogger::log('created', 'Featured Product (Terlaris) - ' . $product->name);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Produk terlaris berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $featuredProduct)
    {
        return view('admin.featured-products.edit', [
            'featuredProduct' => $featuredProduct,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $featuredProduct)
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $featuredProduct->update([
            'status_product' => self::STATUS,
            'no_urut_status' => (int) $validated['sort_order'],
        ]);

        ActivityLogger::log('updated', 'Featured Product (Terlaris) - ' . $featuredProduct->name);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Urutan produk terlaris berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $featuredProduct)
    {
        $productName = $featuredProduct->name;
        $featuredProduct->update([
            'status_product' => null,
            'no_urut_status' => 0,
        ]);

        ActivityLogger::log('deleted', 'Featured Product (Terlaris) - ' . $productName);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Produk terlaris berhasil dihapus.');
    }
}