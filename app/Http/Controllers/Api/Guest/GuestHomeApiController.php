<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use App\Models\Broadcast;
use App\Models\FavoriteBrand;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class GuestHomeApiController extends Controller
{
    public function sync()
    {
        $changedAt = $this->getChangedAt();

        return response()->json([
            'version' => $changedAt?->timestamp ?? 0,
            'changed_at' => $changedAt?->toISOString(),
        ]);
    }

    public function home(Request $request)
    {
        $categories = ProductCategory::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['category_code', 'name', 'image_path', 'updated_at'])
            ->map(fn (ProductCategory $c) => [
                'id' => $c->category_code,
                'category_code' => $c->category_code,
                'name' => $c->name,
                'image_path' => $c->image_path,
                'updated_at' => $c->updated_at?->toISOString(),
            ])
            ->values();

        $favoriteBrandCodes = FavoriteBrand::query()->pluck('product_brand_code')->all();

        $brands = ProductBrand::query()
            ->orderBy('brand_name')
            ->get(['brand_code', 'brand_name', 'brand_image_path', 'updated_at'])
            ->map(function (ProductBrand $brand) use ($favoriteBrandCodes) {
                return [
                    'id' => $brand->brand_code,
                    'brand_code' => $brand->brand_code,
                    'brand_name' => $brand->brand_name,
                    'brand_image_path' => $brand->brand_image_path,
                    'is_favorite' => in_array($brand->brand_code, $favoriteBrandCodes, true),
                    'updated_at' => $brand->updated_at?->toISOString(),
                ];
            })
            ->values();

        $broadcasts = Broadcast::query()
            ->latest()
            ->limit(8)
            ->get(['id', 'image_path', 'description', 'updated_at'])
            ->map(fn (Broadcast $b) => [
                'id' => $b->id,
                'image_path' => $b->image_path,
                'description' => $b->description,
                'updated_at' => $b->updated_at?->toISOString(),
            ])
            ->values();

        $featuredProducts = Product::query()
            ->with(['brand:brand_code,brand_name'])
            ->where('discontinued', false)
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->latest()
            ->limit(10)
            ->get()
            ->map(function (Product $p) {
                return [
                    'id' => $p->id,
                    'sku' => $p->sku,
                    'name' => $p->name,
                    'variant' => $p->variant,
                    'brand' => $p->brand?->brand_name,
                    'image_path' => $p->photo_path,
                    'price_1' => (float) $p->price_1,
                    'updated_at' => $p->updated_at?->toISOString(),
                ];
            })
            ->values();

        $about = AboutPage::query()->latest()->first();

        return response()->json([
            'version' => $this->getChangedAt()?->timestamp ?? 0,
            'categories' => $categories,
            'brands' => $brands,
            'broadcasts' => $broadcasts,
            'featured_products' => $featuredProducts,
            'about' => [
                'content' => $about?->content,
                'updated_at' => $about?->updated_at?->toISOString(),
            ],
        ]);
    }

    private function getChangedAt(): ?Carbon
    {
        $candidates = [
            ProductCategory::query()->max('updated_at'),
            ProductBrand::query()->max('updated_at'),
            Product::query()->max('updated_at'),
            Broadcast::query()->max('updated_at'),
            AboutPage::query()->max('updated_at'),
            FavoriteBrand::query()->max('updated_at'),
        ];

        $timestamps = array_values(array_filter($candidates));

        if ($timestamps === []) {
            return null;
        }

        return Carbon::parse(max($timestamps));
    }
}
