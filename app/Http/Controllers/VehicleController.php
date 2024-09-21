<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return response(['vehicles'=>Vehicle::all()],200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreVehicleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVehicleRequest $request){
        $validatedData = $request->validated();
        $validatedData["id"]=fake()->uuid();
        Vehicle::create($validatedData);
        $vehicle = Vehicle::find($validatedData['id']);
        return response(['message'=>'Vehicle is created','vehicle'=>$vehicle],201);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show(Vehicle $vehicle)
    {
        return response(['vehicle'=>$vehicle],200);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVehicleRequest  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle){
        $validatedData = $request->validated();
        $vehicle->update($validatedData);
        return response(['message'=>'Vehicle is updated','vehicle'=>$vehicle],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle $vehicle
     */
    public function destroy(Vehicle $vehicle){
        $vehicle->delete();
        return response(['message'=>"Vehicle is deleted"],200);
    }
}
