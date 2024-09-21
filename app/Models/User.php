<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'last_active_at',
        'is_active'
    ];
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role'=>Role::class
    ];

    protected $keyType = 'string';

    public static function newestInventoryId(){
        $lastUser = User::all();

        Log::info(count($lastUser));
        if (count($lastUser) == 0) {
            $id = "U0";
        } else{
            $lastUser = User::latest()->first();
            $id = $lastUser["id"];
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

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }
    public function returnOrders()
    {
        return $this->hasMany(ReturnOrder::class, 'customer_id', 'id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'customer_id', 'id');
    }

    public function drug()
    {
        return $this->hasMany(\App\Models\Drug::class, 'user_id', 'id');
    }

    public static function newestUserId(){


        $id = "";
        $lastUser = User::all();

        Log::info(count($lastUser));
        if (count($lastUser) == 0) {
            $id = "US0";
        } else{
            $lastUser = User::latest()->first();
            $id = $lastUser["id"];
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
}
