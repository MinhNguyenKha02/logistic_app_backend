<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReturnOrderRequest;
use App\Http\Requests\UpdateReturnOrderRequest;
use App\Models\ReturnOrder;
use Illuminate\Http\Request;

class ReturnOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return response(['return_orders'=>ReturnOrder::all()],200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreReturnOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreReturnOrderRequest $request){
        $validatedData = $request->validated();
        ReturnOrder::create($validatedData);
        $returnOrder = ReturnOrder::find($validatedData['id']);
        return response(['message'=>'Return order is created','return_order'=>$returnOrder],200);
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
        return response(['message'=>'Return order is updated','return_order'=>$returnOrder],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReturnOrder  $returnOrder
     */
    public function destroy(ReturnOrder $returnOrder){
        $returnOrder->delete();
        return response(['message'=>"Return order is deleted"],200);
    }
}
