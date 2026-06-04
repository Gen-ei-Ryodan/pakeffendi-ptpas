<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class RegionController extends Controller
{
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

        return response()->json(
            $this->fromCache($cacheKey, 'https://wilayah.id/api/districts/'.rawurlencode($regencyCode).'.json', 'regions/districts.json', $regencyCode)
        );
    }

    public function villages(string $districtCode)
    {
        $districtCode = trim($districtCode);
        $cacheKey = 'regions/villages/'.$districtCode.'.json';

        return response()->json(
            $this->fromCache($cacheKey, 'https://wilayah.id/api/villages/'.rawurlencode($districtCode).'.json', 'regions/villages.json', $districtCode)
        );
    }

    /**
     * Read region data from storage or fetch from API.
     *
     * @param  string  $cacheKey       Individual cache file path (e.g. "regions/districts/12.74.json")
     * @param  string  $url            API URL to fetch if cache missing
     * @param  string|null  $combinedKey  Combined file path (e.g. "regions/districts.json") — optional
     * @param  string|null  $subKey       Key inside combined file (e.g. "12.74") — required if $combinedKey set
     */
    private function fromCache(string $cacheKey, string $url, ?string $combinedKey = null, ?string $subKey = null): array
    {
        $disk = Storage::disk('local');

        // 1. Try individual cache file
        if ($disk->exists($cacheKey)) {
            $cached = $disk->get($cacheKey);
            if ($cached !== null && $cached !== '') {
                $decoded = json_decode($cached, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        // 2. Try combined file (e.g. districts.json with all data merged)
        if ($combinedKey !== null && $subKey !== null && $disk->exists($combinedKey)) {
            $combined = $disk->get($combinedKey);
            if ($combined !== null && $combined !== '') {
                $allData = json_decode($combined, true);
                if (is_array($allData) && isset($allData[$subKey])) {
                    // Save individual file for next time
                    $disk->put($cacheKey, json_encode($allData[$subKey]));
                    return $allData[$subKey];
                }
            }
        }

        // 3. Try fetching from API
        try {
            $res = Http::timeout(30)->get($url);
            if ($res->ok()) {
                $json = $res->json();
                $data = is_array($json) && isset($json['data']) ? $json : ['data' => []];
                $disk->put($cacheKey, json_encode($data));
                return $data;
            }
        } catch (\Throwable $e) {
            // HTTP request failed
        }

        return ['data' => []];
    }
}
