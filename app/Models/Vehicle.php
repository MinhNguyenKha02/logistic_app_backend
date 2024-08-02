<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'carrier_id',
        'type',
        'longitude',
        'latitude'
    ];

    protected $keyType = 'string';

    protected $casts = [
        'type'=> VehicleType::class,
    ];

    public function carrier(){
        return $this->belongsTo(User::class, 'carrier_id', 'id');
    }

    public function shipments(){
        return $this->hasMany(Shipment::class, 'vehicle_id', 'id');
    }

}
