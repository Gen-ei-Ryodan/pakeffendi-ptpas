<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class GuestProductApiController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'category_id' => ['nullable', 'string', 'max:50'],
            'brand_id' => ['nullable', 'string', 'max:50'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 20);

        $query = Product::query()
            ->with(['brand:brand_code,brand_name', 'category:category_code,name'])
            ->where('discontinued', false)
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('created_at');

        if (! empty($validated['q'])) {
            $q = trim((string) $validated['q']);
            $words = preg_split('/\s+/', $q);
            
            // Build ranking with word boundary matching using LIKE patterns
            $caseConditions = "CASE ";
            $caseBindings = [];
            
            // 1. Exact match name (highest priority)
            $caseConditions .= "WHEN LOWER(name) = LOWER(?) THEN 1 ";
            $caseBindings[] = $q;
            
            // 2. Exact match SKU
            $caseConditions .= "WHEN LOWER(sku) = LOWER(?) THEN 2 ";
            $caseBindings[] = $q;
            
            // 3. Name starts with keyword (space after ensures word boundary)
            $caseConditions .= "WHEN LOWER(name) LIKE LOWER(?) THEN 3 ";
            $caseBindings[] = $q . ' %';
            
            // 4. Name starts with keyword (no space - covers single word products)
            $caseConditions .= "WHEN LOWER(name) LIKE LOWER(?) THEN 4 ";
            $caseBindings[] = $q . '%';
            
            // 5. Each word starts its own word in the name
            $rankAdded = 4;
            if (count($words) >= 2) {
                // Pattern: starts with first word followed by space
                $caseConditions .= "WHEN LOWER(name) LIKE LOWER(?) THEN " . (++$rankAdded) . " ";
                $caseBindings[] = $words[0] . ' %';
                
                // Pattern: space + first word + space (first word is a complete word in middle)
                $caseConditions .= "WHEN LOWER(name) LIKE LOWER(?) THEN " . (++$rankAdded) . " ";
                $caseBindings[] = '% ' . $words[0] . ' %';
            }
            
            // 6. All other matches
            $caseConditions .= "ELSE " . (++$rankAdded) . " END";
            
            $query->reorder()->orderByRaw($caseConditions, $caseBindings)->orderBy('name');
            
            // Search logic - all words must be present
            foreach ($words as $word) {
                if ($word === '') continue;
                $query->where(function ($qBuilder) use ($word) {
                    $qBuilder->where('name', 'like', "%{$word}%")
                        ->orWhere('sku', 'like', "%{$word}%");
                });
            }
        }

        if (! empty($validated['category_id'])) {
            $query->where('product_category_code', $validated['category_id']);
        }

        if (! empty($validated['brand_id'])) {
            $query->where('product_brand_code', $validated['brand_id']);
        }

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function (Product $p) {
            return [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'variant' => $p->variant,
                'brand' => $p->brand?->brand_name,
                'category' => $p->category?->name,
                'image_path' => $p->photo_path,
                'price_1' => (float) $p->price_1,
                'updated_at' => $p->updated_at?->toISOString(),
            ];
        });

        return response()->json($products);
    }

    public function show(Product $product)
    {
        if ($product->discontinued) {
            abort(404);
        }

        if ($product->category && !$product->category->is_active) {
            abort(404);
        }

        $product->load([
            'brand:brand_code,brand_name',
            'category:category_code,name',
        ]);

        return response()->json([
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'variant' => $product->variant,
            'description' => $product->description,
            'unit' => $product->unit,
            'weight_kg' => (float) $product->weight_kg,
            'brand' => $product->brand?->brand_name,
            'category' => $product->category?->name,
            'photo_path' => $product->photo_path,
            'image_path' => $product->photo_path,
            'price_tiers' => collect($product->pricing_tiers)->map(fn ($t) => [
                'min_qty' => $t['qty_start'],
                'max_qty' => $t['qty_end'],
                'price' => $t['price'],
                'discount_percent' => $t['discount'],
            ])->values(),
            'updated_at' => $product->updated_at?->toISOString(),
        ]);
    }
}
