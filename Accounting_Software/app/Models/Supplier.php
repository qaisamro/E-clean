<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'notes',
        'opening_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    ####################### Relations ############################

    public function bills(): HasMany
    {
        return $this->hasMany(SupplierBill::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function billLines(): HasMany
    {
        return $this->hasManyThrough(SupplierBillLine::class, SupplierBill::class, 'supplier_id', 'bill_id', 'id', 'id');
    }

    ####################### Accounting ############################

    /** Total of every bill ever book for this supplier. */
    public function billsTotal(): float
    {
        return (float) $this->bills()->sum('total');
    }

    /** Total of every payment recorded for this supplier. */
    public function paymentsTotal(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /** Outstanding we owe the supplier (opening + bills − payments). */
    public function balance(): float
    {
        return (float) $this->opening_balance + $this->billsTotal() - $this->paymentsTotal();
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }
}
