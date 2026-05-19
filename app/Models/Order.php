<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'driver_id',
        'vehicle_id',
        'pickup_location_id',
        'delivery_location_id',
        'status',
        'priority',
        'weight_kg',
        'estimated_delivery_time',
        'delivered_at',
        'delivery_notes',
        'delivery_option',
        'tracking_token'
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
            'estimated_delivery_time' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function pickupLocation()
    {
        return $this->belongsTo(Location::class, 'pickup_location_id');
    }

    public function deliveryLocation()
    {
        return $this->belongsTo(Location::class, 'delivery_location_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function deliveryProof()
    {
        return $this->hasOne(DeliveryProof::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}