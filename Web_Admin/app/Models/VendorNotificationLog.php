<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorNotificationLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'array',
        'recipients_count' => 'integer',
        'skipped_count' => 'integer',
        'push_success_count' => 'integer',
        'push_failed_count' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'vendor_notification_log_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'تم الإرسال',
            'partial' => 'تم الإرسال جزئيًا',
            'failed' => 'فشل الإرسال',
            'skipped' => 'لم يُرسل',
            'sending' => 'قيد الإرسال',
            default => (string) $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'success',
            'partial' => 'warning',
            'failed' => 'danger',
            'skipped' => 'secondary',
            'sending' => 'info',
            default => 'light',
        };
    }

    public function getPushStatusLabelAttribute(): string
    {
        return match ($this->push_status) {
            'sent' => 'Push تم',
            'ok' => 'Push تم',
            'partial' => 'Push جزئي',
            'failed', 'error' => 'Push فشل',
            'not_configured' => 'Push غير مهيأ',
            'skipped' => 'Push متخطى',
            default => $this->push_status ? (string) $this->push_status : '—',
        };
    }
}
