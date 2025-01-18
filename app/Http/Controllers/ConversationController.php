<?php

namespace App\Http\Controllers;

use App\Events\OnlineUsers;
use App\Events\SendMessage;
use App\Http\Requests\ConversationRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageSentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\u;

class ConversationController extends Controller
{

    public function sendMessage(Request $request){
        $validatedData = $request->validate([
            'sender_id' => 'required',
            'conversation_id' => 'required',
            'content'=>'required'
        ]);
        $validatedData['id']=fake()->uuid();
        $message = Message::create($validatedData);
        $sender = User::query()->where('id', $validatedData['sender_id'])->first();
        $conversation = Conversation::query()->where('id', $validatedData['conversation_id'])->first();
        if($conversation->userOne->id == Auth::guard("api")->user()->id){
            $conversation->userTwo->notify(new MessageSentNotification($sender, $conversation->userTwo));
        }else{
            $conversation->userOne->notify(new MessageSentNotification($sender, $conversation->userOne));
        }
        broadcast(new SendMessage($validatedData['conversation_id']))->toOthers();
        return response(["message"=>$message],200);
    }

    public function getNotificationsByCurrentUser()
    {
        $notifications = DB::table('notifications')
            ->select('id', 'data', 'created_at','updated_at','read_at')
            ->where('notifiable_id', Auth::guard("api")->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response(["notifications"=>$notifications],200);
    }

    public function markAsRead(Request $request)
    {
        $validatedData = $request->validate([
            'notification_id' => 'required',
        ]);
        $updated = DB::table('notifications')
            ->where('id', $validatedData['notification_id'])
            ->update(['read_at' => now()]);
        if ($updated) {
            return response()->json(['message' => 'Notification marked as read'], 200);
        }
        return response()->json(['message' => 'Notification not found or already updated'], 404);
    }

    public function getMessages(Request $request){
        $validatedData = $request->validate([
            'conversation_id' => 'required',
        ]);

        $messages = Message::where("conversation_id",$validatedData['conversation_id'])->orderBy('created_at', 'asc')->get();

        return response(["messages"=>$messages],200);
    }

    public function getOrCreate(ConversationRequest $request){
        $validatedData = $request->validated();

        $conversation = Conversation::where(function ($query) use ($validatedData) {
            $query->where('user_one_id', $validatedData['user_one_id'])
                ->where('user_two_id', $validatedData['user_two_id']);
        })->orWhere(function ($query) use ($validatedData) {
            $query->where('user_one_id', $validatedData['user_two_id'])
                ->where('user_two_id', $validatedData['user_one_id']);
        })->first();

        if(!$conversation){
            $validatedData['id'] = fake()->uuid();
            Conversation::create($validatedData);
            $conversation = Conversation::where('id', $validatedData['id'])->get()->first();
            return response()->json(["message"=>"Create successfully","conversation"=>$conversation,"user_one"=>$conversation->userOne,"user_two"=>$conversation->userTwo], 201);
        }else{
            return response()->json(["message"=>"Get successfully","conversation"=>$conversation,"user_one"=>$conversation->userOne,"user_two"=>$conversation->userTwo], 200);
        }
    }

    public function updateUserStatus(Request $request){
        $validatedData = $request->validate([
            'user_id' => 'required',
            'is_active'=>'required',
        ]);

        User::where('id', $validatedData['user_id'])
            ->update(['is_active' => $validatedData['is_active']]);

        $user = User::where("id",$validatedData['user_id'])->get()->first();

        broadcast(new OnlineUsers( [] ))->toOthers();
        return response()->json(["users"=>User::all(), "data"=>$validatedData], 200);

    }
}
