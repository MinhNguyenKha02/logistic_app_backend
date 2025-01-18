<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'description',
        'weight',
        'dimensions',
        'category_id'
    ];

    protected $keyType = 'string';

    public function orders()
    {
        return $this->hasmany(Order::class, 'product_id', 'id');
    }

    public function returnOrders(){
        return $this->hasMany(ReturnOrder::class, 'product_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function inventory():HasOne
    {
        return $this->hasOne(Inventory::class);
    }
    public function transactions(){
        return $this->hasMany(Transaction::class, 'product_id', 'id');
    }
    public static function newestProductId(){
        $lastProduct = Product::all();

        Log::info(count($lastProduct));
        if (count($lastProduct) == 0) {
            $id = "PD0";
        } else{
            $lastProduct = Product::latest()->first();
            $id = $lastProduct["id"];
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
