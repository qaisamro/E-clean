<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorAppointmentSetting extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['settings' => 'array'];
}
