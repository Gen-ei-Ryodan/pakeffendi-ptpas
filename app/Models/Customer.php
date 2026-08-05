<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = [
        'customer_code',
        'full_name',
        'account_type',
        'ktp_number',
        'npwp',
        'email',
        'email_verified_at',
        'email_verification_code',
        'password',
        'address',
        'province',
        'city',
        'postal_code',
        'google_maps_url',
        'store_photo_path',
        'phone',
        'contact_person',
        'company_name',
        'internal_code',
        'sales_id',
        'status', // Added status
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Status constants
    public const STATUS_NEW = 'new';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLACKLIST = 'blacklist';

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeBlacklist($query)
    {
        return $query->where('status', self::STATUS_BLACKLIST);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending'); // Backward compatibility
    }

    // Status check methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isNew(): bool
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isBlacklist(): bool
    {
        return $this->status === self::STATUS_BLACKLIST;
    }

    public function canOrder(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function sales()
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function activeAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('is_active', true);
    }
}
