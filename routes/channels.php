<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;
use \Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
//Broadcast::channel('my-channel', function () {
//    return Auth::check();
//});
Broadcast::channel('my-channel', function ($user) {
    return Auth::check() ? $user->toArray() : null;
});


Broadcast::channel('current.users', function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("conversation.{id}", function ($user, $id) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
//    $conversation = Conversation::find($id);
//    if(!$conversation) return false;
//
//    return Auth::guard('api')->check()
//        &&
//        (
//            $conversation->userOne === Auth::guard('api')->user() ||
//            $conversation->userTwo === Auth::guard('api')->user()
//        )
//        ? $user->toArray() : null;
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel("breakdown", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("latestOrder", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("completeOrder", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("createOrder", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("changeOrder", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("changeReturnOrder", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("createSupply", function ($user) {
    return Auth::guard('api')->check() ? $user->toArray() : null;
});

Broadcast::channel("notification.{id}", function ($id) {
    return Auth::guard('api')->check();
});
