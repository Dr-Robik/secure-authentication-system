<?php

namespace App\Models;

use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',
        'phone',

        'role',
        'status',

        'email_verified_at',
        'email_verification_token'
    ];

    protected $hidden = [

        'password',
        'remember_token',
        'email_verification_token'
    ];

    protected function casts(): array
    {
        return [

            'password' => 'hashed',
            'email_verified_at' => 'datetime'
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function customerOrders()
    {
        return $this->hasMany(
            Order::class,
            'customer_id'
        );
    }

    public function driverOrders()
    {
        return $this->hasMany(
            Order::class,
            'driver_id'
        );
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }
}