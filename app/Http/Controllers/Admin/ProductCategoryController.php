<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');

        $categories = ProductCategory::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', [
            'categories' => $categories,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = ProductCategory::create([
            'category_code' => $this->generateCategoryCode($validated['name']),
            'name' => $validated['name'],
            'image_path' => $imagePath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLogger::log('created', 'ProductCategory - '.$category->name);

        return redirect()->route('admin.categories.index')->with('status', 'Category berhasil dibuat.');
    }

    public function edit(ProductCategory $category)
    {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, ProductCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('product_categories', 'name')->ignore($category->category_code, 'category_code')],
            'image' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $category->image_path = $request->file('image')->store('categories', 'public');
        }

        $category->name = $validated['name'];
        $category->is_active = $request->boolean('is_active', true);
        $category->save();

        ActivityLogger::log('updated', 'ProductCategory - '.$category->name);

        return redirect()->route('admin.categories.index')->with('status', 'Category berhasil diupdate.');
    }

    public function destroy(ProductCategory $category)
    {
        $name = $category->name;
        $category->delete();

        ActivityLogger::log('deleted', 'ProductCategory - '.$name);

        return redirect()->route('admin.categories.index')->with('status', 'Category berhasil dihapus.');
    }

    private function generateCategoryCode(string $name): string
    {
        $base = strtoupper(Str::slug($name, '_'));
        $base = $base === '' ? 'CAT' : $base;
        $base = substr($base, 0, 50);

        $code = $base;
        $i = 2;
        while (ProductCategory::query()->where('category_code', $code)->exists()) {
            $suffix = '_'.$i;
            $code = substr($base, 0, 50 - strlen($suffix)).$suffix;
            $i++;
        }

        return $code;
    }
}
