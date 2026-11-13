<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'bill_id',
        'payment_date',
        'amount',
        'method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const METHODS = [
        'cash' => 'نقدي',
        'bank' => 'تحويل بنكي',
        'cheque' => 'شيك',
        'other' => 'أخرى',
    ];

    ####################### Relations ############################

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function methodLabel(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }
}
