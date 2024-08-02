<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
