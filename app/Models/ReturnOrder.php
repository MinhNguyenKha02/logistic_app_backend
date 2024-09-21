<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'product_id',
        'date',
        'reason',
        'status',
        'transaction_id',
    ];

    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function shipments()
    {
        return $this->belongsToMany(Shipment::class, 'return_orders_shipments', 'return_order_id', 'shipment_id')
            ->withTimestamps();
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public static function newestReturnOrderId(){
        $lastReturnOrder = ReturnOrder::all();

        Log::info(count($lastReturnOrder));
        if (count($lastReturnOrder) == 0) {
            $id = "RTO0";
        } else{
            $lastReturnOrder = ReturnOrder::latest()->first();
            $id = $lastReturnOrder["id"];
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
