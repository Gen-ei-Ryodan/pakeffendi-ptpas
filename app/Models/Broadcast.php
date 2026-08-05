<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Broadcast extends Model
{
    protected $fillable = [
        'image_path',
        'description',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return asset('guest/img/placeholder-banner.svg');
        }

        return Str::startsWith($this->image_path, ['http://', 'https://'])
            ? $this->image_path
            : asset('storage/'.$this->image_path);
    }
}
