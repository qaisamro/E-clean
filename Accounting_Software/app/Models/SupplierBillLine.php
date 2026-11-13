<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierBillLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    ####################### Relations ############################

    public function bill(): BelongsTo
    {
        return $this->belongsTo(SupplierBill::class, 'bill_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function isService(): bool
    {
        return is_null($this->product_id);
    }
}
