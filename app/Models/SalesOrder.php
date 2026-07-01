<?php

namespace App\Models;

use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SalesOrder extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_NEW = 'new';
    const STATUS_ON_PROGRESS = 'on_progress';
    const STATUS_ON_DELIVERY = 'on_delivery';
    const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'order_no',
        'order_date',
        'customer_id',
        'payment_type',
        'status',
        'sales_person_id',
        'sales_id',
        'shipping_fee',
        'grand_total',
        'dpp',
        'ppn',
        'ppn_percent',
        'process_date',
        'process_time',
        'process_order_no',
        'notes',
        'delivery_to',
        'delivery_address',
        'delivery_phone',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'process_date' => 'date',
        'shipping_fee' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'dpp' => 'decimal:2',
        'ppn' => 'decimal:2',
        'ppn_percent' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public static function nextOrderNo(?Carbon $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'W' . $date->format('ymd');

        // Mengambil nomor pesanan terakhir untuk prefix hari ini (WYYMMDD)
        // Menggunakan orderByDesc('order_no') agar benar-benar mendapatkan nomor urut tertinggi
        $lastOrderNo = self::query()
            ->where('order_no', 'like', $prefix . '%')
            ->orderByDesc('order_no')
            ->value('order_no');

        $next = 1;
        if (is_string($lastOrderNo) && strlen($lastOrderNo) >= 10) {
            // Mengambil 4 digit terakhir
            $lastSuffix = (int) substr($lastOrderNo, -4);
            $next = $lastSuffix + 1;
        }

        if ($next > 9999) {
            throw new \RuntimeException('Batas nomor pesanan harian terlampaui untuk prefix ' . $prefix);
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public static function createWithNextOrderNo(array $attributes): self
    {
        $orderDate = $attributes['order_date'] ?? now();
        $orderDate = $orderDate instanceof Carbon ? $orderDate : Carbon::parse($orderDate);
        $attributes['order_date'] = $orderDate;

        for ($attempt = 1; $attempt <= 10; $attempt++) {
            $attributes['order_no'] = self::nextOrderNo($orderDate);

            try {
                return self::query()->create($attributes);
            } catch (QueryException $e) {
                $code = (string) ($e->errorInfo[1] ?? '');
                if (($e->getCode() === '23000' || $code === '1062') && $attempt < 10) {
                    continue;
                }

                throw $e;
            }
        }

        throw new \RuntimeException('Failed to generate unique order number.');
    }
}
