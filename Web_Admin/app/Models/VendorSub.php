<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Link between a child ("sub") vendor account and the store it belongs to.
 * The child user keeps the regular users row; this table only records which
 * store the owner created it for.
 */
class VendorSub extends Model
{
    protected $fillable = ['vendor_id', 'user_id'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
