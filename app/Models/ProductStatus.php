<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStatus extends Model
{
    protected $fillable = [
        'code',
        'name',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
