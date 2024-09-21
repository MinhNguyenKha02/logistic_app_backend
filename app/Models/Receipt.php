<?php

namespace App\Models;

use App\Enums\ReceiptStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_id',
        'status',
        'price',
    ];

    protected $keyType = 'string';

    protected $casts = [
        'status' => ReceiptStatus::class
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
