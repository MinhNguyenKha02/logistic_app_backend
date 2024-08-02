<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(["warehouses"=>Warehouse::all()], 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreWarehouseRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWarehouseRequest $request)
    {
        $validatedData = $request->validated();
        Warehouse::create($validatedData);
        $warehouse=Warehouse::find($validatedData['id']);
        return response(["message"=>"Warehouse is created", "warehouse"=>$warehouse], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function show(Warehouse $warehouse)
    {
        return response(["warehouse"=>$warehouse], 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateWarehouseRequest  $request
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse)
    {
        $validatedData = $request->validated();
        $warehouse->update($validatedData);
        return response(["message" => "Warehouse is updated", "warehouse" => $warehouse], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Warehouse  $warehouse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return response(["message"=>"Warehouse is deleted"], 200);
    }

    public function checkWarehouseFull()
    {
        return;
    }
}
