<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(["shipments"=>Shipment::all()], 200);
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
        Shipment::create($validated);
        $shipment=Shipment::find($validated["id"]);
        return response(["message"=>"Shipments is created", "shipment"=>$shipment], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Shipment  $shipment
     * @return \Illuminate\Http\Response
     */
    public function show(Shipment $shipment)
    {
        return response(["shipment"=>$shipment], 200);
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
