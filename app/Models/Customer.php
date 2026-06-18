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
        'phone',
        'contact_person',
        'company_name',
        'internal_code',
        'sales_id',
        'status', // Added status
    ];

    protected $hidden = [
        'password',
    ];

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive()
    {
        return $this->status === 'active';
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
