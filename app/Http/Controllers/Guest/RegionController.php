<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RegionController extends Controller
{
    public function provinces()
    {
        return response()->json($this->fetch('regions.provinces', 'https://wilayah.id/api/provinces.json'));
    }

    public function regencies(string $provinceCode)
    {
        $provinceCode = trim($provinceCode);
        return response()->json($this->fetch(
            'regions.regencies.'.$provinceCode,
            'https://wilayah.id/api/regencies/'.rawurlencode($provinceCode).'.json'
        ));
    }

    public function districts(string $regencyCode)
    {
        $regencyCode = trim($regencyCode);
        return response()->json($this->fetch(
            'regions.districts.'.$regencyCode,
            'https://wilayah.id/api/districts/'.rawurlencode($regencyCode).'.json'
        ));
    }

    public function villages(string $districtCode)
    {
        $districtCode = trim($districtCode);
        return response()->json($this->fetch(
            'regions.villages.'.$districtCode,
            'https://wilayah.id/api/villages/'.rawurlencode($districtCode).'.json'
        ));
    }

    private function fetch(string $cacheKey, string $url): array
    {
        return Cache::remember($cacheKey, now()->addDay(), function () use ($url) {
            $res = Http::timeout(15)->get($url);
            if (! $res->ok()) {
                return ['data' => []];
            }
            $json = $res->json();
            if (! is_array($json) || ! isset($json['data'])) {
                return ['data' => []];
            }
            // Map id to code for compatibility with existing JS
            $mapped = array_map(function ($item) {
                return [
                    'code' => $item['id'] ?? $item['code'] ?? '',
                    'name' => $item['name'] ?? ''
                ];
            }, $json['data']);
            return ['data' => $mapped];
        });
    }
}
