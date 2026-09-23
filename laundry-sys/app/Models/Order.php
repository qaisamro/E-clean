<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'payment_type',
        'total_price',
        'paid_money',
        'remaining_money',
        'has_deferred_payment',
        'status',
        'payment_status',
        'customer_id',
        'user_id',
        'created_by_customer',
        'deliver_date',
        'discount',
        'notes',
        'deliver_time',
        'actual_delivery_date',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'payment_type' => PaymentType::class
    ];

    protected static function booted()
    {
        self::creating(function (Order $order) {
            // Assign temporary order code first
            $order->order_code = uniqid('temp_');

            if (auth()->check()) {
                $order->user_id = auth()->id();
            } else {
                $order->created_by_customer = true;
            }
        });

        self::created(function (Order $order) {
            $prefix = cache()->rememberForever('order-prefix', function () {
                try {
                    return \App\Models\Setting::where('key', 'order-prefix')->value('value') ?? 'E';
                } catch (\Throwable $e) {
                    return 'E';
                }
            });
            $order->order_code = $prefix . $order->id;
            $order->saveQuietly();
        });
    }

    ########################## Relations ##########################

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function getReadableOrderStatusAttribute(): string
    {
        return $this->status?->label() ?? '';
    }

    public function getReadablePaymentStatusAttribute(): string
    {
        return $this->payment_status?->label() ?? '';
    }
}
