<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Events\MyEvent;
use App\Events\OnlineUser;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            $user = User::query()->paginate(3);
            return response(["users"=>$user], 200);
        }else{
            $user = User::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "users" => $user
            ], 200);
        }
    }
    public function users(){
        return response(["users"=>User::all()], 200);
    }

    public function show(User $user)
    {
        // server will automatically return 404 when its not match inventory when using binding
        return response(["user"=>$user],200);
    }

    public function search(Request $request){
        $keyword = $request['keyword'];
        $user = User::query()->where("id", "like", "%$keyword%")
            ->orWhere("name", "like", "%$keyword%")
            ->orwhere("role", "like", "%$keyword%")
            ->orWhere("email", "like", "%$keyword%")
            ->paginate(3);
        if($user){
            return response(["users"=>$user], 200);
        }
    }

    public function order(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            return response(User::query()->paginate(3), 200);
        }else{
            return response(User::query()->orderBy($type, $direction)->paginate(3), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInventoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["id"] = fake()->uuid();
        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);

        $user = User::find($validatedData['id']);
        return response(["message"=>"User is created", 'user'=>$user],201);
    }


    /**
     * Update the specified resource in storage.
     * @param  \App\Http\Requests\UpdateInventoryRequest  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user->update($validatedData);
        return response(["message"=>"User is updated", 'user'=>$user],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response(["message"=>"User is deleted"],204);
    }

    public function role()
    {
        return response(["roles"=>Role::cases()], 200);
    }

    public function register(StoreUserRequest $request)
    {
        $values = new \ReflectionClass(Role::class);
        $values = $values->getConstants();
        $validatedData = $request->validated();
//        $validatedData['role'] = $values[array_rand($values)];

        Log::info("Data ",$validatedData);

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
        $data = $request->only(['email','password']);
        $user = User::where('email', $data['email'])->first();
        if($user && Hash::check($data['password'], $user->password)){
            broadcast(new OnlineUser($user))->toOthers();
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token, 'user'=>$user], 200);
        }else{
            return response(['message'=>'Invalid credentials'], 401);
        }
    }

    public function getSessionData()
    {
        return response()->json(['value'=>session('token')], 200);
    }


    public function logout(Request $request)
    {
        Log::info("inside logout");
        if (Auth::guard("api")->user()) {
            // Revoke the user's current access token
            Auth::guard("api")->user()->token()->revoke();

            return response()->json(['message' => 'Logout success'], 200);
        }

        return response()->json(['message' => 'No user authenticated'], 401);
    }
    public function getCurrentUser()
    {
        return Auth::guard('api')->user();
    }

    public function getOnlineUsers(Request $request){
        $users = User::where("id","!=",Auth::guard("api")->user()->id)->get();
        return response()->json(["users"=>$users], 200);
    }

    public  function otp()
    {
        $otp = rand(100000, 999999);
        Cache::put('otp', $otp, now()->addMinutes(10));
        $otp = Cache::get('otp');
        Mail::send('emails.my_otp', ['otp' => $otp], function ($message) {
            $message->to(Auth::guard('api')->user()->email)->subject('Your OTP Code');
        });
        return response(["otp"=>$otp, "message"=>"Send successfully otp"], 200);
    }

    function confirmOTP(Request $request){
        $otp = Cache::get('otp');
        $validatedData = $request->validate([
            'otp' => 'required',
        ]);
        if($otp==$validatedData['otp']){
            return response(["message"=>"OTP is valid"], 200);
        }else{
            return response(["message"=>"OTP is invalid"], 400);
        }
    }

}
