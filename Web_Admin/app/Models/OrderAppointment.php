<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAppointment extends Model
{
    protected $guarded = ['id'];
    // UTC strings are intentionally independent of the legacy application's timezone.
    protected $casts = ['reschedule_history' => 'array'];
}
