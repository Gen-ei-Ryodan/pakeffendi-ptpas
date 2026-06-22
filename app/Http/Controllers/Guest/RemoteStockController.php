<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\RemoteStockService;
use Illuminate\Http\Request;

class RemoteStockController extends Controller
{
    public function index(RemoteStockService $remoteStock, Request $request)
    {
        $connection = $remoteStock->testConnection();

        $perPage = 50;
        $page = (int) ($request->get('page', 1));
        $search = $request->get('q');

        $query = Product::query()
            ->with(['brand:brand_code,brand_name'])
            ->active()
            ->orderBy('sku');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $totalProducts = $query->count();
        $products = $query->paginate($perPage, ['*'], 'page', $page)->withQueryString();

        $skus = $products->pluck('sku')->toArray();
        $stockMap = $remoteStock->getStockBatch($skus);

        $matchedOnPage = collect($products->items())->filter(fn($p) => isset($stockMap[$p->sku]))->count();

        return view('guest.remote-stock.index', [
            'connection' => $connection,
            'products' => $products,
            'stockMap' => $stockMap,
            'totalProducts' => $totalProducts,
            'matchedOnPage' => $matchedOnPage,
            'search' => $search,
        ]);
    }
}
