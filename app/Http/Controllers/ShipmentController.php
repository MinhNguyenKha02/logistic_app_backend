<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\Status;
use App\Events\breakDown;
use App\Events\changeOrder;
use App\Events\changeReturnOrder;
use App\Events\createOrder;
use App\Events\latestOrder;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\ReturnOrder;
use App\Models\Shipment;
use App\Models\User;
use App\Notifications\MessageOrderSampleNotification;
use App\Notifications\MessageShipmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentController extends Controller
{


    public function search(Request $request){
        $keyword = $request['keyword'];
        $shipment = Shipment::query()->where('id', "like", "%$keyword%")
            ->orWhere('vehicle_id', "like", "%$keyword%")
            ->orwhere("date", "like", "%$keyword%")
            ->orwhere("status", "like", "%$keyword%")
            ->orWhere("capacity", "like", "%$keyword%")
            ->orWhere("estimated_arrival_time", "like", "%$keyword%")
            ->orWhere("arrival_time", "like", "%$keyword%")
            ->orWhere("origin_address", "like", "%$keyword%")
            ->orWhere("destination_address", "like", "%$keyword%")
            ->orWhereHas('vehicle.carrier', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->orWhereHas("orders", function ($query) use ($keyword) {
                $query->where('orders.id', 'like', "%$keyword%");
            })
            ->orWhereHas("return_orders", function ($query) use ($keyword) {
                $query->where('return_orders.id', 'like', "%$keyword%");
            })
            ->orWhere(DB::raw("DATE(created_at)"), '=', $keyword)
            ->orWhere(DB::raw("DATE(updated_at)"), '=', $keyword)
            ->paginate(3);
        if($shipment){
            return response(["shipments"=>$shipment], 200);
        }else{
            return response(["message" => "Not Found"], 404);
        }
    }
    public function getLatestShipment(){
        $shipment = Shipment::query()->latest()->first();
        return response(['shipment'=>$shipment],200);
    }

    public function order(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            return response(Shipment::query()->paginate(3), 200);
        }else{
            return response(Shipment::query()->orderBy($type, $direction)->paginate(3), 200);
        }
    }

    public function shipmentDetailDelivery(Shipment $shipment, Request $request)
    {
        $is_order=true;
        $validatedData = $request->validate([
            'status' => 'required',
        ]);
        $shipment->update(["status"=>"success"]);
        $orders = $shipment->orders;
        if (!$orders || $orders->isEmpty()) {
            $is_order=false;
            $orders = $shipment->return_orders;
        }
        if(!$is_order){
            foreach ($orders as $order) {
                $inventory = Inventory::query()->where("id", $order->product->inventory->id)->first();
                Log::info($inventory);
                $inventory?->update(['quantity' => $inventory->quantity - $order->transaction->quantity]);
                $inventory->save();
                $order->update(["status"=>"returned"]);
                $order->transaction->update(["status"=>"returned"]);
            }
        }else{
            foreach ($orders as $order) {
                $inventory = Inventory::query()->where("id", $order->product->inventory->id)->first();
                Log::info($inventory);
                $inventory?->update(['quantity' => $inventory->quantity - $order->transaction->quantity]);
                $inventory->save();
                $order->update($validatedData);
                $order->transaction->update($validatedData);
            }
        }
        broadcast(new changeOrder())->toOthers();
        broadcast(new changeReturnOrder())->toOthers();
        foreach ($orders as $order) {
            $message = "Your order is " . (!$is_order ? "returned" : $validatedData['status']);
            $order->customer->notify(new MessageOrderSampleNotification($message, Auth::guard('api')->user(), $order->customer));
        }
        $shipment->vehicle->carrier->notify(new MessageShipmentNotification("success", Auth::guard('api')->user(), $shipment->vehicle->carrier));
        return response(["message"=>"update successfully"],200);
    }

    public function addBreakDownShipment(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required',
            'date'=>'required',
            'status' =>'required',
            'estimated_arrival_time' => 'required',
            'arrival_time'=>'required',
            'capacity'=>'required',
            'origin_address'=>'required',
            'destination_address'=>'required',
            'order_id'=>'required'
        ]);
        $validatedData['id']=fake()->uuid();
        $order = Order::query()->where("id", $validatedData['order_id'])->first();
        if(!$order)
            $order = ReturnOrder::query()->where("id", $validatedData['order_id'])->first();
        Shipment::create([
            'id'=>$validatedData['id'],
            'vehicle_id' => $validatedData['vehicle_id'],
            'date' => $validatedData['date'],
            'status' => $validatedData['status'],
            'estimated_arrival_time' => $validatedData['estimated_arrival_time'],
            'arrival_time' => $validatedData['arrival_time'],
            'capacity' => $validatedData['capacity'],
            'origin_address' => $validatedData['origin_address'],
            'destination_address' => $validatedData['destination_address'],
        ]);
        $shipment=Shipment::query()->where("id", $validatedData['id'])->first();
        $order->shipments()->attach($shipment, ["id"=>fake()->uuid(), 'created_at'=>now(), 'updated_at'=>now()]);
        if($validatedData['vehicle_id']!="" || $shipment->vehicle!=null )
            $shipment->vehicle->carrier->notify(new MessageShipmentNotification($validatedData['status'], Auth::guard('api')->user(), $shipment->vehicle->carrier));
        foreach ($shipment->orders as $order) {
            $order->customer->notify(new MessageOrderSampleNotification("Your order is being processed by breakdown event", Auth::guard('api')->user(), $order->customer));
        }
        broadcast(new latestOrder())->toOthers();
        return response()->json(["message"=>"Create shipment breakdown succesfully"], 201);

    }

    public function breakdown(Request $request)
    {
        $validatedData = $request->validate([
            'origin' => 'required',
            'order_id'=>'required',
        ]);

        $order = Order::query()->where('id', $validatedData['order_id'])->first();
        if(!$order)
            $order = ReturnOrder::query()->where('id', $validatedData['order_id'])->first();
        Log::info($order. "\n".$order->customer."\n".$order->shipments[0]->vehicle->carrier);
        broadcast(new breakDown($order->id, $order->shipments[0], $validatedData['origin']))->toOthers();
        $order->customer->notify(new MessageOrderSampleNotification("Your order is breakdown", Auth::guard('api')->user(), $order->customer));
        $order->shipments[0]->vehicle->carrier->notify(new MessageOrderSampleNotification("Your currently order is breakdown", Auth::guard('api')->user(), $order->shipments[0]->vehicle->carrier));
        return response()->json([
            'message'=>'Notify'
        ], 200);

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
            $shipments = Shipment::query()->paginate(3);
            return response(["shipments"=>$shipments], 200);
        }else{
            $shipments = Shipment::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "shipments" => $shipments
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreShipmentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShipmentRequest $request)
    {
        $validated = $request->validated();
        $validated["id"] = fake()->uuid();
        Shipment::create($validated);
        $shipment=Shipment::find($validated["id"]);
        Order::latest()->first()->shipments()->attach($shipment['id'], ["id"=>fake()->uuid(), 'created_at'=>now(), 'updated_at'=>now()]);
        broadcast(new createOrder())->toOthers();
        return response(["message"=>"Shipments is created", "shipment"=>$shipment], 201);
    }

    public function storeOrderShipment(StoreShipmentRequest $request)
    {
        $validated = $request->validated();
        $validated["id"] = fake()->uuid();
        Shipment::create($validated);
        $shipment=Shipment::find($validated["id"]);
        Order::latest()->first()->shipments()->attach($shipment['id'], ["id"=>fake()->uuid(), 'created_at'=>now(), 'updated_at'=>now()]);
        broadcast(new createOrder())->toOthers();
        $onlineUsers = User::query()->where('is_active', 1)->where("role", Role::Employee)->get();
        foreach ($onlineUsers as $user) {
            $user->notify(new MessageOrderSampleNotification("You have new order",Auth::guard('api')->user(), $user));
        }

        return response(["message"=>"Shipments is created", "shipment"=>$shipment], 201);
    }

    public function storeReturnOrderShipment(StoreShipmentRequest $request)
    {
        $validated = $request->validated();
        $validated["id"] = fake()->uuid();
        Shipment::create($validated);
        $shipment=Shipment::find($validated["id"]);
        ReturnOrder::latest()->first()->shipments()->attach($shipment['id'], ["id"=>fake()->uuid(), 'created_at'=>now(), 'updated_at'=>now()]);
        broadcast(new createOrder())->toOthers();
        $onlineUsers = User::query()->where('is_active', 1)->where("role", Role::Employee)->get();
        foreach ($onlineUsers as $user) {
            $user->notify(new MessageOrderSampleNotification("You have new return order",Auth::guard('api')->user(), $user));
        }
        return response(["message"=>"Shipments is created", "shipment"=>$shipment], 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function show(Shipment $shipment)
    {
        return response(["shipment"=>$shipment->load("orders")], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateShipmentRequest  $request
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        $validated = $request->validated();
        $shipment->update($validated);
        if (isset($validated['vehicle_id']) && $validated['vehicle_id'] !== "")
            $shipment->vehicle->carrier->notify(new MessageShipmentNotification($validated['status'], Auth::guard("api")->user(), $shipment->vehicle->carrier));
        if(isset($validated['origin_address_breakdown']) && $validated['origin_address_breakdown'] !==""){
            Cache::put("origin_address_breakdown", $validated['origin_address_breakdown']);
        }
        return response(["message"=>"Shipment is updated", "shipment"=>$shipment], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Shipment $shipment)
    {
        if ($shipment->orders->isNotEmpty()) {
            $orders = $shipment->orders;

            foreach ($orders as $order) {
                $relatedShipments = $order->shipments;

                // Detach all orders from the shipments
                $relatedShipments->each(function ($relatedShipment) {
                    $relatedShipment->orders()->detach();
                });

                $relatedShipments->each->delete();
            }
        }

        if ($shipment->return_orders->isNotEmpty()) {
            $orders = $shipment->return_orders;

            foreach ($orders as $order) {
                $relatedShipments = $order->shipments;

                // Detach all orders from the shipments
                $relatedShipments->each(function ($relatedShipment) {
                    $relatedShipment->return_orders()->detach();
                });

                $relatedShipments->each->delete();
            }
        }

        $shipment->delete();
        return response(["message"=>"Shipment is deleted"], 204);
    }


}
