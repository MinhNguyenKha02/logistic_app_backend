<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['id','user_one_id', 'user_two_id', 'last_message_at'];

    protected $keyType = 'string';


    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    // Relationship with Message model
    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public static function newestInventoryId(){
        $lastConversation = Conversation::all();

        Log::info(count($lastConversation));
        if (count($lastConversation) == 0) {
            $id = "U0";
        } else{
            $lastConversation = Conversation::latest()->first();
            $id = $lastConversation["id"];
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
