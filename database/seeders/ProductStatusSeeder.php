<?php

namespace Database\Seeders;

use App\Models\ProductStatus;
use Illuminate\Database\Seeder;

class ProductStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['code' => 'TERLARIS', 'name' => 'Terlaris', 'sort_order' => 1],
            ['code' => 'TERBARU', 'name' => 'Terbaru', 'sort_order' => 2],
            ['code' => 'PROMO', 'name' => 'Promo', 'sort_order' => 3],
            ['code' => 'REKOMENDASI', 'name' => 'Rekomendasi', 'sort_order' => 4],
        ];

        foreach ($statuses as $status) {
            ProductStatus::firstOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
