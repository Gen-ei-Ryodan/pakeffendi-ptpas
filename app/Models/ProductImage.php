<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'product_variant_item_id',
        'image_path',
        'sort_order',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function variantItem(): BelongsTo
    {
        return $this->belongsTo(ProductVariantItem::class, 'product_variant_item_id');
    }

    public function getImageUrlAttribute(): string
    {
        $placeholder = asset('guest/img/placeholder-product.svg');

        if (! $this->image_path) {
            return $placeholder;
        }

        if (Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        $path = str_replace(' ', '%20', $this->image_path);

        if (! Storage::disk('public')->exists($this->image_path)) {
            return $placeholder;
        }

        return asset('storage/'.$path);
    }
}
