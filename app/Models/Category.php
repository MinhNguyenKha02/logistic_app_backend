<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable =[
        'id',
        'name',
    ];

    protected $keyType = 'string';

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function drugs(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Drug::class, 'drugs_categories')->withTimestamps();
    }
}
