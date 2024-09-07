<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'address',
        'capacity',
        'longitude',
        'latitude'
    ];

    protected $keyType = 'string';

    public function inventories() : HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id', 'id');
    }
    public static function newestWarehouseId(){

        $id = "";
        $lastWarehouse = Warehouse::all();

        Log::info(count($lastWarehouse));
        if (count($lastWarehouse) == 0) {
            $id = "WH0";
        } else{
            $lastWarehouse = Warehouse::latest()->first();
            $id = $lastWarehouse["id"];
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
