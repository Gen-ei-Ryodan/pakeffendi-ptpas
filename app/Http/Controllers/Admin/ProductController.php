<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $products = Product::query()
            ->with(['brand', 'category'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($query) use ($q) {
                    $query
                        ->where('sku', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('variant', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.products.create', [
            'brands' => ProductBrand::query()->orderBy('brand_name')->get(),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $variant = trim((string) $request->input('variant', ''));
        $request->merge(['variant' => $variant]);

        $validated = $request->validate([
            'photo' => ['nullable', 'image', 'max:4096'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->where(fn ($q) => $q->where('variant', $variant))],
            'variant' => ['nullable', 'string', 'max:100'],
            'product_brand_code' => ['required', 'string', 'max:50', 'exists:product_brands,brand_code'],
            'product_category_code' => ['required', 'string', 'max:50', 'exists:product_categories,category_code'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'discontinued' => ['nullable', 'boolean'],
            'price_1' => ['required', 'numeric', 'min:0.01'],
            'price_2' => ['nullable', 'numeric', 'min:0.01'],
            'price_3' => ['nullable', 'numeric', 'min:0.01'],
            'qty_1' => ['nullable', 'integer', 'min:0'],
            'disc_1' => ['nullable', 'numeric', 'min:0'],
            'qty_2' => ['nullable', 'integer', 'min:1'],
            'disc_2' => ['nullable', 'numeric', 'min:0'],
            'qty_3' => ['nullable', 'integer', 'min:1'],
            'disc_3' => ['nullable', 'numeric', 'min:0'],
            'status_product' => ['nullable', 'string', 'max:50'],
            'no_urut_status' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::create([
            ...$validated,
            'discontinued' => (bool) ($validated['discontinued'] ?? false),
            'photo_path' => null,
            'no_urut_status' => (int) ($validated['no_urut_status'] ?? 0),
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $originalName = basename((string) $file->getClientOriginalName());
            $filename = preg_replace('/\s+/', '-', $originalName);
            $path = $file->storeAs('products', $filename, 'public');
            $product->photo_path = $path;
            $product->save();
        }

        ActivityLogger::log('created', 'Product - '.$product->sku);

        return redirect()->route('admin.products.index')->with('status', 'Product berhasil dibuat.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', [
            'product' => $product,
            'brands' => ProductBrand::query()->orderBy('brand_name')->get(),
            'categories' => ProductCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $variant = trim((string) $request->input('variant', ''));
        $request->merge(['variant' => $variant]);

        $validated = $request->validate([
            'photo' => ['nullable', 'image', 'max:4096'],
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->where(fn ($q) => $q->where('variant', $variant))->ignore($product->id)],
            'variant' => ['nullable', 'string', 'max:100'],
            'product_brand_code' => ['required', 'string', 'max:50', 'exists:product_brands,brand_code'],
            'product_category_code' => ['required', 'string', 'max:50', 'exists:product_categories,category_code'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'discontinued' => ['nullable', 'boolean'],
            'price_1' => ['required', 'numeric', 'min:0.01'],
            'price_2' => ['nullable', 'numeric', 'min:0.01'],
            'price_3' => ['nullable', 'numeric', 'min:0.01'],
            'qty_1' => ['nullable', 'integer', 'min:0'],
            'disc_1' => ['nullable', 'numeric', 'min:0'],
            'qty_2' => ['nullable', 'integer', 'min:1'],
            'disc_2' => ['nullable', 'numeric', 'min:0'],
            'qty_3' => ['nullable', 'integer', 'min:1'],
            'disc_3' => ['nullable', 'numeric', 'min:0'],
            'status_product' => ['nullable', 'string', 'max:50'],
            'no_urut_status' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $originalName = basename((string) $file->getClientOriginalName());
            $filename = preg_replace('/\s+/', '-', $originalName);
            $path = $file->storeAs('products', $filename, 'public');
            $product->photo_path = $path;
        }

        $product->fill([
            ...$validated,
            'discontinued' => (bool) ($validated['discontinued'] ?? false),
        ]);
        $product->save();

        ActivityLogger::log('updated', 'Product - '.$product->sku);

        return redirect()->route('admin.products.index')->with('status', 'Product berhasil diupdate.');
    }

    public function destroy(Product $product)
    {
        $sku = $product->sku;
        $product->delete();

        ActivityLogger::log('deleted', 'Product - '.$sku);

        return redirect()->route('admin.products.index')->with('status', 'Product berhasil dihapus.');
    }
}
