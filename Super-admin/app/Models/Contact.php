<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone_number', 'email', 'message'];

    /**
     * Get the notifications for this contact
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_id')
            ->where('related_type', 'contact');
    }
}
