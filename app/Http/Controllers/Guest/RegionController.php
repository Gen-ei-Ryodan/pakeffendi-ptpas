<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RegionController extends Controller
{
    /**
     * Fetch region data from wilayah.id API and cache to storage files.
     * Once cached, no further HTTP calls are needed (works offline).
     */
    public function provinces()
    {
        return response()->json($this->fromCache('regions/provinces.json', 'https://wilayah.id/api/provinces.json'));
    }

    public function regencies(string $provinceCode)
    {
        $provinceCode = trim($provinceCode);
        $cacheKey = 'regions/regencies/'.$provinceCode.'.json';

        return response()->json($this->fromCache($cacheKey, 'https://wilayah.id/api/regencies/'.rawurlencode($provinceCode).'.json'));
    }

    public function districts(string $regencyCode)
    {
        $regencyCode = trim($regencyCode);
        $cacheKey = 'regions/districts/'.$regencyCode.'.json';

        return response()->json($this->fromCache($cacheKey, 'https://wilayah.id/api/districts/'.rawurlencode($regencyCode).'.json'));
    }

    public function villages(string $districtCode)
    {
        $districtCode = trim($districtCode);
        $cacheKey = 'regions/villages/'.$districtCode.'.json';

        return response()->json($this->fromCache($cacheKey, 'https://wilayah.id/api/villages/'.rawurlencode($districtCode).'.json'));
    }

    /**
     * Read from storage file if exists; otherwise fetch from URL and save to storage.
     * Storage disk: 'local' → storage/app/regions/...
     */
    private function fromCache(string $cacheKey, string $url): array
    {
        $disk = Storage::disk('local');

        if ($disk->exists($cacheKey)) {
            $cached = $disk->get($cacheKey);

            if ($cached !== null && $cached !== '') {
                $decoded = json_decode($cached, true);

                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        try {
            $res = Http::timeout(30)->get($url);
            if ($res->ok()) {
                $json = $res->json();
                $data = is_array($json) && isset($json['data']) ? $json : ['data' => []];

                // Cache to storage file
                $disk->put($cacheKey, json_encode($data));

                return $data;
            }
        } catch (\Throwable $e) {
            // HTTP request failed — return empty if no cache exists
        }

        return ['data' => []];
    }
}
