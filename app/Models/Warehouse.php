<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'address',
        'capacity',
        'longitude',
        'latitude'
    ];

    protected $keyType = 'string';

    public function inventories() : HasMany
    {
        return $this->hasMany(Inventory::class, 'warehouse_id', 'id');
    }
}
