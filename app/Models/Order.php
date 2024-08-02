<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'product_id',
        'date',
        'transaction_id',
        'status'
    ];

    protected $casts = [
        'status' => Status::class
    ];

    public function orderDetail()
    {
        return $this->hasOne(OrderDetail::class, 'order_id', 'id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function transaction(){
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function shipments(){
        return $this->belongsToMany(Shipment::class, 'orders_shipments', 'order_id', 'shipment_id');
    }
}
