<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'date',
        'transaction_id',
        'status',
        'note',
        'order_failed_times'
    ];

    protected $casts = [
        'status' => Status::class
    ];

    public function receipt(){
        return $this->hasOne(Receipt::class, 'order_id', 'id');
    }

    public function orderDetail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id', 'id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function shipments(){
        return $this->belongsToMany(Shipment::class, 'orders_shipments', 'order_id', 'shipment_id')->orderBy('created_at', 'asc');
    }
    public static function newestOrderId(){
        $lastOrder = Order::all();

        Log::info(count($lastOrder));
        if (count($lastOrder) == 0) {
            $id = "OD0";
        } else{
            $lastOrder = Order::latest()->first();
            $id = $lastOrder["id"];
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
