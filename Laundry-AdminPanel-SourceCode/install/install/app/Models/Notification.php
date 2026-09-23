<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'is_read' => 'boolean',
    ];

    /**
     * Get the customer that owns the notification
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the user (admin) that owns the notification
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
        return $this;
    }

    /**
     * Scope to get only unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get admin notifications
     */
    public function scopeAdmin($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope to get customer notifications
     */
    public function scopeCustomer($query)
    {
        return $query->whereNotNull('customer_id');
    }

    /**
     * Get unread notifications count for user/admin
     */
    public static function getUnreadCountForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread notifications for user/admin
     */
    public static function getUnreadForUser($userId)
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->latest('created_at')
            ->get();
    }
}
