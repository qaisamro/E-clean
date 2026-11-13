<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'bill_number',
        'bill_date',
        'due_date',
        'total',
        'paid',
        'remaining',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'bill_date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2',
        'paid' => 'decimal:2',
        'remaining' => 'decimal:2',
    ];

    ####################### Relations ############################

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SupplierBillLine::class, 'bill_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class, 'bill_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    ####################### Accounting ############################

    /** Keep paid/remaining/status in sync with recorded payments. */
    public static function recalculate(self $bill): void
    {
        $paid = (float) $bill->payments()->sum('amount');
        $total = (float) $bill->total;
        $remaining = max(0, $total - $paid);
        $status = 'unpaid';
        if ($paid > 0) {
            $status = $remaining <= 0.0001 ? 'paid' : 'partial';
        }
        $bill->update([
            'paid' => $paid,
            'remaining' => $remaining,
            'status' => $status,
        ]);
    }

    public function remainingDue(): float
    {
        return max(0, (float) $this->total - (float) $this->paid);
    }
}
