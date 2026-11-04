<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Roles;
use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'password',
        'role',
        'status',
        'job_title',
        'salary',
        'hire_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'role' => Roles::class,
        'status' => UserStatus::class,
        'hire_date' => 'date',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === Roles::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === Roles::ADMIN;
    }

    public function isCashier(): bool
    {
        return $this->role === Roles::CASHIER;
    }

    public function isEmployee(): bool
    {
        return $this->role === Roles::EMPLOYEE;
    }

    public function isAdministrator(): bool
    {
        return $this->role === Roles::ADMIN || $this->role === Roles::SUPER_ADMIN;
    }

    public function isSuspended(): bool
    {
        return $this->status === UserStatus::SUSPENDED;
    }
}
