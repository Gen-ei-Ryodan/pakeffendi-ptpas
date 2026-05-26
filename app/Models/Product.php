<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $appends = ['photo_url', 'has_photo'];

    protected $fillable = [
        'sku',
        'name',
        'variant',
        'product_brand_code',
        'product_category_code',
        'description',
        'unit',
        'weight_kg',
        'discontinued',
        'photo_path',
        'price_1',
        'price_2',
        'price_3',
        'qty_1',
        'disc_1',
        'qty_2',
        'disc_2',
        'qty_3',
        'disc_3',
        'status_product',
        'no_urut_status',
    ];

    protected $casts = [
        'discontinued' => 'boolean',
        'weight_kg' => 'decimal:2',
        'price_1' => 'decimal:2',
        'price_2' => 'decimal:2',
        'price_3' => 'decimal:2',
        'disc_1' => 'decimal:2',
        'disc_2' => 'decimal:2',
        'disc_3' => 'decimal:2',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_code', 'brand_code');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_code', 'category_code');
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id')
            ->withPivot('relation_type');
    }

    public function scopeActive($query)
    {
        return $query->where('discontinued', false);
    }

    public function scopeHasPhoto($query)
    {
        return $query->whereNotNull('photo_path')->where('photo_path', '!=', '');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status_product', $status)->orderBy('no_urut_status');
    }

    public function pricingForQuantity(int $quantity): array
    {
        $qty = max(0, $quantity);
        $qty2 = $this->qty_2 ? (int) $this->qty_2 : null;
        $qty3 = $this->qty_3 ? (int) $this->qty_3 : null;

        if ($qty3 && $qty >= $qty3) {
            $unitPrice = (float) ($this->price_3 ?? $this->price_2 ?? $this->price_1);
            $discountPercent = (float) ($this->disc_3 ?? 0);
        } elseif ($qty2 && $qty >= $qty2) {
            $unitPrice = (float) ($this->price_2 ?? $this->price_1);
            $discountPercent = (float) ($this->disc_2 ?? 0);
        } else {
            $unitPrice = (float) $this->price_1;
            $discountPercent = (float) ($this->disc_1 ?? 0);
        }

        $netPrice = $unitPrice * (1 - ($discountPercent / 100));

        return [
            'unit_price' => $unitPrice,
            'discount_percent' => $discountPercent,
            'net_price' => $netPrice,
        ];
    }

    public function getPricingTiersAttribute(): array
    {
        $tiers = [];

        $price1 = (float) $this->price_1;
        $disc1 = (float) ($this->disc_1 ?? 0);
        $tiers[] = [
            'qty_start' => 1,
            'qty_end' => $this->qty_2 ? ($this->qty_2 - 1) : null,
            'price' => $price1,
            'discount' => $disc1,
            'net_price' => $price1 * (1 - ($disc1 / 100)),
        ];

        if ($this->price_2 && $this->qty_2) {
            $price2 = (float) $this->price_2;
            $disc2 = (float) ($this->disc_2 ?? 0);
            $tiers[] = [
                'qty_start' => (int) $this->qty_2,
                'qty_end' => $this->qty_3 ? ($this->qty_3 - 1) : null,
                'price' => $price2,
                'discount' => $disc2,
                'net_price' => $price2 * (1 - ($disc2 / 100)),
            ];
        }

        if ($this->price_3 && $this->qty_3) {
            $price3 = (float) $this->price_3;
            $disc3 = (float) ($this->disc_3 ?? 0);
            $tiers[] = [
                'qty_start' => (int) $this->qty_3,
                'qty_end' => null,
                'price' => $price3,
                'discount' => $disc3,
                'net_price' => $price3 * (1 - ($disc3 / 100)),
            ];
        }

        return $tiers;
    }

    public function getHasPhotoAttribute(): bool
    {
        return ! empty($this->photo_path) && ! Str::startsWith($this->photo_path, 'https://placehold.co');
    }

    public function getPhotoUrlAttribute(): string
    {
        if (! $this->photo_path) {
            return asset('guest/img/placeholder-product.svg');
        }

        if (Str::startsWith($this->photo_path, ['http://', 'https://'])) {
            return $this->photo_path;
        }

        return asset('storage/'.str_replace(' ', '%20', $this->photo_path));
    }
}
