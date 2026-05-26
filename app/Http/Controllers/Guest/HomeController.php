<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\Broadcast;
use App\Models\FavoriteBrand;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use App\Models\ProductStatus;

class HomeController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::query()
            ->orderBy('name')
            ->get();

        $favoriteBrandCodes = FavoriteBrand::query()->pluck('product_brand_code')->all();

        $brands = ProductBrand::query()
            ->orderBy('brand_name')
            ->get()
            ->map(function (ProductBrand $brand) use ($favoriteBrandCodes) {
                $brand->is_favorite = in_array($brand->brand_code, $favoriteBrandCodes, true);
                return $brand;
            });

        $broadcasts = Broadcast::query()->latest()->limit(8)->get();

        $featuredProducts = Product::query()
            ->with(['brand', 'images'])
            ->active()
            ->hasPhoto()
            ->orderBy('name')
            ->limit(10)
            ->get();

        $statuses = ProductStatus::query()->orderBy('sort_order')->get();
        $statusProducts = collect();
        foreach ($statuses as $status) {
            $products = Product::query()
                ->with(['brand', 'images'])
                ->active()
                ->hasPhoto()
                ->byStatus($status->code)
                ->limit(8)
                ->get();
            if ($products->isNotEmpty()) {
                $statusProducts->push([
                    'status' => $status,
                    'products' => $products,
                ]);
            }
        }

        $about = AboutPage::query()->latest()->first();

        return view('guest.home', [
            'categories' => $categories,
            'brands' => $brands,
            'broadcasts' => $broadcasts,
            'featuredProducts' => $featuredProducts,
            'statusProducts' => $statusProducts,
            'about' => $about,
        ]);
    }

    public function product(Product $product)
    {
        abort_if($product->discontinued, 404);

        $categories = ProductCategory::query()
            ->orderBy('name')
            ->get();

        $favoriteBrandCodes = FavoriteBrand::query()->pluck('product_brand_code')->all();

        $brands = ProductBrand::query()
            ->orderBy('brand_name')
            ->get()
            ->map(function (ProductBrand $brand) use ($favoriteBrandCodes) {
                $brand->is_favorite = in_array($brand->brand_code, $favoriteBrandCodes, true);
                return $brand;
            });

        $broadcasts = Broadcast::query()->latest()->limit(8)->get();

        $featuredProducts = Product::query()
            ->with(['brand', 'images'])
            ->active()
            ->hasPhoto()
            ->orderBy('name')
            ->limit(10)
            ->get();

        $statuses = ProductStatus::query()->orderBy('sort_order')->get();
        $statusProducts = collect();
        foreach ($statuses as $status) {
            $products = Product::query()
                ->with(['brand', 'images'])
                ->active()
                ->hasPhoto()
                ->byStatus($status->code)
                ->limit(8)
                ->get();
            if ($products->isNotEmpty()) {
                $statusProducts->push([
                    'status' => $status,
                    'products' => $products,
                ]);
            }
        }

        $about = AboutPage::query()->latest()->first();

        return view('guest.home', [
            'categories' => $categories,
            'brands' => $brands,
            'broadcasts' => $broadcasts,
            'featuredProducts' => $featuredProducts,
            'statusProducts' => $statusProducts,
            'about' => $about,
            'initialScreen' => 'productDetail',
            'initialProductId' => $product->id,
        ]);
    }
}
