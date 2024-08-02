<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
