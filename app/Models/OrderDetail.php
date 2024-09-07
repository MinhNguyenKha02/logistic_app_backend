<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'id',
        'order_id',
        'quantity',
        'price',
        'unit'
    ];

    protected $keyType = 'string';

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public static function newestOrderDetailId(){
        $lastOrderDetail = OrderDetail::all();

        Log::info(count($lastOrderDetail));
        if (count($lastOrderDetail) == 0) {
            $id = "ODD0";
        } else{
            $lastOrderDetail = OrderDetail::latest()->first();
            $id = $lastOrderDetail["id"];
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
