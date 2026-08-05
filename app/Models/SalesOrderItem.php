<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'net_price',
        'discount_percent',
        'final_total',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'net_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'final_total' => 'decimal:2',
    ];

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
