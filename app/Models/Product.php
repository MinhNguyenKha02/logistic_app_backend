<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        return $this->hasOne(Inventory::class, 'product_id', 'id');
    }
    public function transactions(){
        return $this->hasMany(Transaction::class, 'product_id', 'id');
    }
}
