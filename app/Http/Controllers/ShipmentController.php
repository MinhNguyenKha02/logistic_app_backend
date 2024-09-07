<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;
use Illuminate\Http\Request;

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
            ->paginate(3);
        if($shipment){
            return response($shipment, 200);
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
            return response(Shipment::query()->paginate(3), 200);
        }else{
            return response(Shipment::query()->orderBy($type, $direction)->paginate(3), 200);
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
        $validated["id"] = Shipment::newestShipmentId();
        Shipment::create($validated);
        $shipment=Shipment::find($validated["id"]);
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
        $shipment->delete();
        return response(["message"=>"Shipment is deleted"], 200);
    }
}
