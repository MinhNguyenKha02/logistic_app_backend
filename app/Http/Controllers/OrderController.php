<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\Status;
use App\Events\changeOrder;
use App\Events\completeOrder;
use App\Events\createOrder;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Receipt;
use App\Models\ReturnOrder;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\MessageOrderNotification;
use App\Notifications\MessageOrderSampleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function search(Request $request){
        $keyword = $request['keyword'];

        $shipment = Order::query()->where('id', "like", "%$keyword%")
            ->orWhere('customer_id', "like", "%$keyword%")
            ->orwhere("date", "like", "%$keyword%")
            ->orWhere("transaction_id", "like", "%$keyword%")
            ->orWhere("status", "like", "%$keyword%")
            ->orWhereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->orWhere(DB::raw("DATE(created_at)"), '=', $keyword)
            ->orWhere(DB::raw("DATE(updated_at)"), '=', $keyword)
            ->paginate(3);
        if($shipment){
            return response(["orders"=>$shipment], 200);
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
            return response(Order::query()->paginate(3), 200);
        }else{
            return response(Order::query()->orderBy($type, $direction)->paginate(3), 200);
        }
    }
    public function status(){
        return response(["status"=>Status::cases()],200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            $inventory = Order::query()->paginate(3);
            return response(["orders"=>$inventory], 200);
        }else{
            $inventory = Order::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "orders" => $inventory
            ], 200);
        }
    }

    public function getOrderById(Request $request)
    {
        $validatedData = $request->validate([
            'order_id' => 'required'
        ]);
        $order = Order::query()->where("id", $validatedData["order_id"])->first();
        return response([
            "receipt"=> $order->receipt,
            "order"=>$order,
            "shipments"=>$order->shipments()->orderBy('orders_shipments.created_at', 'asc')->get(),
            "transaction"=>$order->transaction,
            "product"=>$order->transaction->product,
            "category"=>$order->transaction->product->category
        ], 200);
    }

    public function getLatestOrderByCurrentUser(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
        ]);
        if (Auth::guard("api")->user()->role == Role::Driver){
            $vehicle = Vehicle::query()->where("carrier_id", $validatedData['user_id'])->first();
            $shipments = Shipment::query()
                ->where("vehicle_id", $vehicle->id)
                ->orderBy("created_at", "desc")
                ->get();
            $shipmentIds = $shipments->pluck('id')->toArray();
            $latestOrderId = DB::table('orders_shipments')
                ->whereIn('shipment_id', $shipmentIds)
                ->orderBy('created_at', 'desc')
                ->value('order_id');
            $shipments = Shipment::query()->whereIn("id", $shipmentIds)->orderBy('created_at', 'asc')->get();
            $latestOrder = Order::query()->where("id", $latestOrderId)->first();

            $receipt = Receipt::query()->where("order_id", $latestOrder->id)->orderBy("created_at", "desc")->first();
            return response([
                "receipt"=> $receipt,
                "order"=>$latestOrder,
                "shipments"=>[$shipments],
                "transaction"=>$latestOrder->transaction,
                "category"=>$latestOrder->transaction->product->category
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }else {
            $latestOrder = Order::query()->where("customer_id", $validatedData['user_id'])->orderBy('updated_at', 'desc')->first();
            return response([
                "receipt"=> $latestOrder->receipt,
                "order"=>$latestOrder,
                "shipments"=>$latestOrder->shipments()->orderBy('orders_shipments.created_at', 'asc')->get(),
                "transaction"=>$latestOrder->transaction,
                "category"=>$latestOrder->transaction->product->category
            ], 200);
        }
    }

    public function confirmOrder(Request $request, Order $order)
    {
        $validatedData = $request->validate([
            'status' => 'required',
        ]);

        $order->update($validatedData);
        $order->shipments[0]->update($validatedData);
        broadcast(new changeOrder())->toOthers();
        $customer = User::query()->where("id", $order->customer->id)->first();
        $customer->notify(new MessageOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        return response(["message"=>"Change successfully"], 200);
    }

    public function updateOrderDelivery(Order $order, Request $request)
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
        broadcast(new changeOrder())->toOthers();
        $customer = User::query()->where("id", $order->customer->id)->first();
        $customer->notify(new MessageOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        return response(["message"=>"Update successfully"], 200);
    }

    public function getOrdersByCurrentUser()
    {
        $user = Auth::guard("api")->user();

        if($user->role==Role::Driver){
            $vehicle = Vehicle::query()->where("carrier_id", $user->id)->orderBy("created_at", "desc")->first();
            $shipments = Shipment::query()->where("vehicle_id", $vehicle->id)->orderBy("created_at", "desc")->get();
            $shipmentIds = $shipments->pluck('id')->toArray();
            $orderIds = DB::table("orders_shipments")
                ->whereIn("shipment_id", $shipmentIds)
                ->pluck("order_id")
                ->toArray();
            $orders = Order::query()
                ->whereIn("id", $orderIds)
                ->orderBy('updated_at', 'desc')
                ->get();
            return response(["orders"=>$orders], 200);
        }else{
            $orders=Order::query()->where("customer_id", $user->id)->orderBy('updated_at', 'desc')->get();
            return response(["orders"=>$orders], 200);
        }
    }

    public function latestOrder(){
        return response(["order"=>Order::latest()->first()],200);
    }


    public function fetchOrderBreakdown(Request $request)
    {
        $is_breakdown = false;
        $_index = 0;
        $validatedData = $request->validate([
            'order_id' => 'required'
        ]);
        $order = Order::query()->where("id", $validatedData["order_id"])->first();
        if(!$order)
            $order = ReturnOrder::query()->where("id", $validatedData["order_id"])->first();
        foreach ($order->shipments as $index => $shipment) {
            if($shipment->status===Status::Breakdown) {
                $is_breakdown = true;
                $_index=$index;
                break;
            }
        }
        if($is_breakdown) {
            $value=Cache::get("origin_address_breakdown", "");
            if($value!="") {
                $order->shipments[$_index]['origin_address'] = $value;
                Cache::forget("origin_address_breakdown");
            }
            return response(["order" => $order, "shipment" => $order->shipments[$_index]], 200);
        }
        else return response(["message"=>"Order is not breakdown"], 204);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData["id"] = fake()->uuid();
        Order::create($validatedData);
        $order=Order::query()->where("id",$validatedData["id"])->first();
        return response(["message"=>"Order is created", 'inventory'=>$order],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return response(["order"=>$order],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $validatedData = $request->validated();
        if($validatedData['status']=='paid'){
            Mail::send('emails.my_custom_email', [
                'name' => Auth::guard("api")->user()->name,
                'paragraph' => 'We\'d like to thank you for your experience with us. Have a nice time, ' . Auth::guard("api")->user()->name . '!'
            ], function ($message) {
                $message->to(Auth::guard('api')->user()->email)->subject('Thankful for your experience with us!');
            });
        }
        $order->update($validatedData);
        broadcast(new completeOrder())->toOthers();
        broadcast(new changeOrder())->toOthers();
        if($validatedData['status']!=""){
            $customer = User::query()->where("id", $order->customer->id)->first();
            $customer->notify(new MessageOrderNotification($validatedData['status'], Auth::guard("api")->user(), $customer));
        }
        return response(["message"=>"Order is updated", 'order'=>$order],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {

        $order->receipt->delete();
        $order->transaction->delete();
        $order->shipments()->detach();
        $order->shipments->each->delete();
        $order->delete();
        return response(["message"=>"Order is deleted"],204);
    }
}
