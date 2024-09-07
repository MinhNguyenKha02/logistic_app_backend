<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'warehouse_id',
        'product_id',
        'quantity',
        'unit'
    ];

    protected $keyType = 'string';


    public function warehouse(): BelongsTo {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public static function newestInventoryId(){
        $lastInventory = Inventory::all();

        Log::info(count($lastInventory));
        if (count($lastInventory) == 0) {
            $id = "IV0";
        } else{
            $lastInventory = Inventory::latest()->first();
            $id = $lastInventory["id"];
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

    public static function find($keyword)
    {
        return Inventory::where('id', "like", "%$keyword%")
                        ->orWhere('warehouse_id', "like", "%$keyword%")
                        ->orWhere('product_id', "like", "%$keyword%")
                        ->orWhere('unit', "like", "%$keyword%")
                        ->orWhere('quantity', "like", "%$keyword%")
                        ->orWhere('created_at', "like", "%$keyword%")
                        ->orWhere('updated_at', "like", "%$keyword%")
                        ->orWhere('created_at', "=", "$keyword")
                        ->orWhere("updated_at", "=", "$keyword")
                        ->get();
    }
}
