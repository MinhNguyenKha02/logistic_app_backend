<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Events\changeReturnOrder;
use App\Events\completeOrder;
use App\Http\Requests\StoreReturnOrderRequest;
use App\Http\Requests\UpdateReturnOrderRequest;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\ReturnOrder;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\MessageOrderSampleNotification;
use App\Notifications\MessageReturnOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReturnOrderController extends Controller
{
    public function search(Request $request){
        $keyword = $request['keyword'];
        $shipment = ReturnOrder::query()->where('id', "like", "%$keyword%")
            ->orWhere('customer_id', "like", "%$keyword%")
            ->orwhere("product_id", "like", "%$keyword%")
            ->orwhere("date", "like", "%$keyword%")
            ->orwhere("reason", "like", "%$keyword%")
            ->orWhere("transaction_id", "like", "%$keyword%")
            ->orWhere("status", "like", "%$keyword%")
            ->orWhereHas('product', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->orWhereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->orWhere(DB::raw("DATE(created_at)"), '=', $keyword)
            ->orWhere(DB::raw("DATE(updated_at)"), '=', $keyword)
            ->paginate(3);
        if($shipment){
            return response(["return_orders"=>$shipment], 200);
        }else{
            return response(["message" => "Not Found"], 404);
        }
    }
    public function order(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            return response(ReturnOrder::query()->paginate(3), 200);
        }else{
            return response(ReturnOrder::query()->orderBy($type, $direction)->paginate(3), 200);
        }
    }

    public function confirmReturnOrder(Request $request, ReturnOrder $order)
    {
        $validatedData = $request->validate([
            'status' => 'required',
        ]);

        $order->update($validatedData);
        $order->shipments[0]->update($validatedData);
        broadcast(new changeReturnOrder())->toOthers();
        $customer = User::query()->where("id", $order->customer->id)->first();
        $customer->notify(new MessageReturnOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        return response(["message"=>"Change successfully"], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            $inventory = ReturnOrder::query()->paginate(3);
            return response(["return_orders"=>$inventory], 200);
        }else{
            $inventory = ReturnOrder::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "return_orders" => $inventory
            ], 200);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReturnOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReturnOrderRequest $request){
        $validatedData = $request->validated();
        $validatedData["id"]=fake()->uuid();
        ReturnOrder::create($validatedData);
        $returnOrder = ReturnOrder::find($validatedData['id']);
        return response(['message'=>'Return order is created','return_order'=>$returnOrder],201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     * @return \Illuminate\Http\Response
     */
    public function show(ReturnOrder $returnOrder)
    {
        return response(['return_order'=>$returnOrder],200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateReturnOrderRequest  $request
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateReturnOrderRequest $request, ReturnOrder $returnOrder){
        $validatedData = $request->validated();
        $returnOrder->update($validatedData);
        broadcast(new changeReturnOrder())->toOthers();
        if($validatedData['status']!=""){
            $customer = User::query()->where("id", $returnOrder->customer->id)->first();
            $customer->notify(new MessageReturnOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        }
        return response(['message'=>'Return order is updated','return_order'=>$returnOrder],200);
    }

    public function updateReturnOrderDelivery(ReturnOrder $order, Request $request)
    {
        $validatedData = $request->validate([
            'status' => 'required',
        ]);
        if($validatedData['status']=='paid'){
            Mail::send('emails.my_custom_email', [
                'name' => Auth::guard("api")->user()->name,
                'paragraph' => 'We\'d like to thank you for your experience with us. Have a nice time, ' . Auth::guard("api")->user()->name . '!'
            ], function ($message) {
                $message->to(Auth::guard('api')->user()->email)->subject('Thankful for your experience with us!');
            });
        }
        $order->update($validatedData);
        $order->transaction->update(["status"=>"success"]);
        broadcast(new completeOrder())->toOthers();
        broadcast(new changeReturnOrder())->toOthers();
        $customer = User::query()->where("id", $order->customer->id)->first();
        $customer->notify(new MessageReturnOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        return response(["message"=>"Update successfully"], 200);
    }

    public function getLatestReturnOrderByCurrentUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
        ]);
        if (Auth::guard("api")->user()->role == Role::Driver){
            $vehicle = Vehicle::query()->where("carrier_id", $validatedData['user_id'])->first();
            $shipment = Shipment::query()->where("vehicle_id", $vehicle->id)->orderBy("created_at", "desc")->first();
            $latestOrder = ReturnOrder::query()->where("id", $shipment->orders[0]->pivot->order_id)->orderBy("created_at", "desc")->first();
            return response([
                "order"=>$latestOrder,
                "shipments"=>[$shipment],
                "transaction"=>$latestOrder->transaction,
                "product"=>$latestOrder->product,
                "category"=>$latestOrder->product->category
            ], 200);
        }else {
            $latestOrder = ReturnOrder::query()->where("customer_id", $validatedData['user_id'])->orderBy('updated_at', 'desc')->first();
            return response([
                "order"=>$latestOrder,
                "shipments"=>$latestOrder->shipments()->orderBy('return_orders_shipments.created_at', 'asc')->get(),
                "transaction"=>$latestOrder->transaction,
                "product"=>$latestOrder->product,
                "category"=>$latestOrder->product->category
            ], 200);
        }

    }

    public function getReturnOrderById(Request $request)
    {
        $validatedData = $request->validate([
            'return_order_id' => 'required'
        ]);
        $order = ReturnOrder::query()->where("id", $validatedData["return_order_id"])->first();
        return response([
            "return_order"=>$order,
            "shipments"=>$order->shipments()->orderBy('return_orders_shipments.created_at', 'asc')->get(),
            "transaction"=>$order->transaction,
            "product"=>$order->product,
            "category"=>$order->product->category
        ], 200);
    }

    public function getReturnOrdersByCurrentUser()
    {
        $user = Auth::guard("api")->user();

        if($user->role==Role::Driver){
            $vehicle = Vehicle::query()->where("carrier_id", $user->id)->orderBy("created_at", "desc")->first();
            $shipments = Shipment::query()->where("vehicle_id", $vehicle->id)->orderBy("created_at", "desc")->get();
            $shipmentIds = $shipments->pluck('id')->toArray();
            $orderIds = DB::table("return_orders_shipments")
                ->whereIn("shipment_id", $shipmentIds)
                ->pluck("return_order_id")
                ->toArray();
            $orders = ReturnOrder::query()
                ->whereIn("id", $orderIds)
                ->orderBy("updated_at", "desc")
                ->get();
            return response(["return_orders"=>$orders], 200);
        }else{
            $orders=ReturnOrder::query()->where("customer_id", $user->id)->orderBy('updated_at', 'desc')->get();
            return response(["return_orders"=>$orders], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     */
    public function destroy(ReturnOrder $returnOrder){
        $returnOrder->transaction->delete();
        $returnOrder->shipments()->detach();
        $returnOrder->shipments->each->delete();
        $returnOrder->delete();
        return response(['message'=>"Return order is deleted"],200);
    }
}
