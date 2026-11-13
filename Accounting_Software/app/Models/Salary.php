<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'description',
        'user_id',
        'basic_salary',
        'deductions',
        'bonuses',
        'payment_date',
        'payment_status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'basic_salary' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
