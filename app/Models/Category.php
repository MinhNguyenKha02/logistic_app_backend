<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

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

    public static function newestCategoryId(){
        $lastCategory = Category::all();

        Log::info(count($lastCategory));
        if (count($lastCategory) == 0) {
            $id = "IV0";
        } else{
            $lastCategory = Category::latest()->first();
            $id = $lastCategory["id"];
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

    public static function find($keyword)
    {
        return Category::where("name", "like", "%$keyword%")
                                ->orWhere("id", "like", "%$keyword%")->get();
    }

}
