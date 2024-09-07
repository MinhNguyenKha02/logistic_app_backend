<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        event(new MyEvent(["name"=>"bccccc"]));
        return "ok";
    }
    public function users(){
        return response(["users"=>User::all()], 200);
    }

    public function register(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["id"]=User::newestUserId();
        User::create($validatedData);
        $user = User::find($validatedData["id"]);
        return response(["message"=>"User is created","user"=>$user],201);
    }

    public function changeUserInfo(UpdateUserRequest $request)
    {
        $validatedData = $request->validated();
        Auth::user()->update($validatedData);
        return response(["message"=>"Update is successful"], 200);
    }

    public function login(Request $request){
        $data = $request->all();
        if(Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']]) ){
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            session()->put('token', $token);
            Log::warning("Controller, token ". session("token"));
            return response()->json(['token' => $token, 'user'=>$user], 200);
        }else{
            response(['message'=>'Invalid credentials'], 401);
        }
    }

    public function getSessionData()
    {
        return response()->json(['value'=>session('token')], 200);
    }


    public function logout()
    {
        cookie()->forget('token');
        session()->forget('token');
        Auth::logout();
        return response(['message'=>'Logout success'], 200);
    }
    public function getCurrentUser()
    {
        return Auth::guard('api')->user();
    }
}
