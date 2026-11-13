<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, (new DriverOrder())->getTable())
            ->join('drivers', 'drivers.id', '=', 'driver_orders.driver_id')
            ->whereColumn('orders.vendor_id', 'drivers.vendor_id')
            ->select('orders.*')
            ->withPivot('is_accept', 'status')
            ->withTimestamps();
    }

    public function orderHistories()
    {
        return $this->belongsToMany(Order::class, (new DriverHistory())->getTable())
            ->join('drivers', 'drivers.id', '=', 'driver_histories.driver_id')
            ->whereColumn('orders.vendor_id', 'drivers.vendor_id')
            ->select('orders.*')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function driverDevices()
    {
        return $this->hasMany(DriverDeviceKey::class);
    }

    // --------- scope ---------------
    public function scopeIsApprove(Builder $builder, $isApprove = true): Builder
    {
        return $builder->where('is_approve', $isApprove);
    }
}
