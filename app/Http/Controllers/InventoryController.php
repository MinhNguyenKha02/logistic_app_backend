<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartialUpdateInventoryRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\Warehouse;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\ErrorHandler\Debug;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(['inventories'=>Inventory::all()], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreInventoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInventoryRequest $request)
    {
        $validatedData = $request->validated();
        Inventory::create($validatedData);

        $inventory = Inventory::find($validatedData['id']);
        return response(["message"=>"Inventory is created", 'inventory'=>$inventory],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        // server will automatically return 404 when its not match inventory when using binding
        return response(["inventory"=>$inventory],200);
    }


    /**
     * Update the specified resource in storage.
     * @param  \App\Http\Requests\UpdateInventoryRequest  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        $validatedData = $request->validated();
        $inventory->update($validatedData);
        return response(["message"=>"Inventory is updated", 'inventory'=>$inventory],200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return response(["message"=>"Inventory is deleted"],200);
    }
}
