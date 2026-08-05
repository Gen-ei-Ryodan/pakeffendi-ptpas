<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProduct;
use App\Models\Product;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeaturedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $featuredProducts = FeaturedProduct::query()
            ->with('product:id,name,sku,photo_path')
            ->orderBy('sort_order')
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
            ->whereNotIn('id', FeaturedProduct::pluck('product_id'))
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
            'product_id' => ['required', 'integer', 'exists:products,id', 'unique:featured_products,product_id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.unique' => 'Produk sudah ada di daftar produk terlaris.',
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $featuredProduct = FeaturedProduct::create($validated);

        ActivityLogger::log('created', 'Featured Product - ' . $featuredProduct->product->name);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Produk terlaris berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FeaturedProduct $featuredProduct)
    {
        $featuredProduct->load('product:id,name,sku');

        return view('admin.featured-products.edit', [
            'featuredProduct' => $featuredProduct,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FeaturedProduct $featuredProduct)
    {
        $validated = $request->validate([
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ], [
            'sort_order.required' => 'Urutan wajib diisi.',
            'sort_order.integer' => 'Urutan harus berupa angka.',
        ]);

        $featuredProduct->update($validated);

        ActivityLogger::log('updated', 'Featured Product - ' . $featuredProduct->product->name);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Urutan produk terlaris berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeaturedProduct $featuredProduct)
    {
        $productName = $featuredProduct->product->name;
        $featuredProduct->delete();

        ActivityLogger::log('deleted', 'Featured Product - ' . $productName);

        return redirect()->route('admin.featured-products.index')
            ->with('status', 'Produk terlaris berhasil dihapus.');
    }
}
