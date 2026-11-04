<?php

namespace App\Models;

use App\Enums\ExpensesType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'user_id',
        'value',
        'description',
    ];

    protected $casts = [
        'type' => ExpensesType::class
    ];

    ########################## Relations ##########################
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
