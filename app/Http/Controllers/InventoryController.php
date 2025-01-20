<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartialUpdateInventoryRequest;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Notifications\MessageOrderSampleNotification;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Enums\Unit;
use Symfony\Component\ErrorHandler\Debug;

class InventoryController extends Controller
{


    public function getLatest(){
        return \response("inventory", 200);
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
            $inventory = Inventory::query()->paginate(3);
            return response(["inventories"=>$inventory], 200);
        }else{
            $inventory = Inventory::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "inventories" => $inventory
            ], 200);
        }
//        return response(['inventories'=>Inventory::all()], 200);


    }
    public function search(Request $request){
        $keyword = $request['keyword'];
        $inventory = Inventory::query()->where("warehouse_id", "like", "%$keyword%")
                                        ->orWhere("product_id", "like", "%$keyword%")
                                        ->orwhere("quantity", "like", "%$keyword%")
                                        ->orwhere("unit", "like", "%$keyword%")
                                        ->orWhere("id", "like", "%$keyword%")
                                        ->orWhereHas('warehouse', function ($query) use ($keyword) {
                                            $query->where('name', 'like', "%$keyword%");
                                        })
                                        ->orWhereHas('product', function ($query) use ($keyword) {
                                            $query->where('name', 'like', "%$keyword%");
                                        })
                                        ->orWhere(DB::raw("DATE(created_at)"), '=', $keyword)
                                        ->orWhere(DB::raw("DATE(updated_at)"), '=', $keyword)
                                        ->paginate(3);
        if($inventory){
            return response(["inventories"=>$inventory], 200);
        }
    }

    public function order(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            return response(Inventory::query()->paginate(3), 200);
        }else{
            return response(Inventory::query()->orderBy($type, $direction)->paginate(3), 200);
        }
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
        $validatedData["id"] = fake()->uuid();
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
        $inventory->product->delete();
        $inventory->delete();
        return response(["message"=>"Inventory is deleted"],204);
    }

    public function units()
    {
        return response(["units"=>Unit::cases()], 200);
    }

}
