<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'warehouse_id',
        'product_id',
        'quantity',
        'unit'
    ];

    protected $keyType = 'string';


    public function warehouse(): BelongsTo {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
