<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Category::class, 'drugs_categories')->withTimestamps();
    }
}
