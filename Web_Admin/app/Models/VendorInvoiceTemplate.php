<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorInvoiceTemplate extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['settings' => 'array'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
