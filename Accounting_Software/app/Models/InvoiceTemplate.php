<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceTemplate extends Model
{
    protected $table = 'pos_invoice_templates';

    protected $fillable = ['settings', 'logo_path'];

    protected $casts = [
        'settings' => 'array',
    ];
}
