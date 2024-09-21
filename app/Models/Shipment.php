<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
    public function return_orders(){
        return $this->belongsToMany(ReturnOrder::class, 'return_orders_shipments', 'shipment_id', 'return_order_id')
            ->withTimestamps();
    }


    public static function newestShipmentId()
    {
        $id = "";
        $lastShipment = Shipment::all();

        Log::info(count($lastShipment));
        if (count($lastShipment) == 0) {
            $id = "SM0";
        } else{
            $lastShipment = Shipment::latest()->first();
            $id = $lastShipment["id"];
        }
        $pattern = "/\d+/";
        $match="";
        if(preg_match($pattern, $id, $match)){
            Log::info($match);
            $number = (int)$match[0]+1;
            Log::info("before replace".$id.", ".$number.", ".$match[0]);
            $id=str_replace($match[0],(string)$number,$id);
            Log::info("replaced".$id.", ".$number.", ".$match[0]);
            return $id;
        }
    }

}
