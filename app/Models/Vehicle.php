<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    public static function newestVehicleId(){
        $id = "";
        $lastVehicle = Vehicle::all();

        Log::info(count($lastVehicle));
        if (count($lastVehicle) == 0) {
            $id = "VH0";
        } else{
            $lastVehicle = Vehicle::latest()->first();
            $id = $lastVehicle["id"];
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
