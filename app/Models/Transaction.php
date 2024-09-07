<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
    ];

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'product_id',
        'date',
        'type',
        'status',
        'quantity',
        'unit'
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'transaction_id', 'id');
    }
    public static function newestTransactionId(){

        $id = "";
        $lastTransaction = Transaction::all();

        Log::info(count($lastTransaction));
        if (count($lastTransaction) == 0) {
            $id = "TS0";
        } else{
            $lastTransaction = Transaction::latest()->first();
            $id = $lastTransaction["id"];
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
