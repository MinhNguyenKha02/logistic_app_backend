<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'vehicle_id',
        'date',
        'status',
        'capacity',
        'estimated_arrival_time',
        'arrival_time',
        'origin_address',
        'destination_address',
    ];

    protected $keyType = 'string';

    protected $casts = [
        'status' => Status::class
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }



    public function orders(){
        return $this->belongsToMany(Order::class, 'orders_shipments', 'shipment_id', 'order_id')
            ->withTimestamps();
    }

}
