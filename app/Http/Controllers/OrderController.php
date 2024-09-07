<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use Nyholm\Psr7\Request;

class OrderController extends Controller
{
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
        $keyword = $request["keyword"];

        return response(["orders"=>Order::all()], 200);
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
        $validatedData["id"] = Order::newestOrderId();
        Order::create($validatedData);

        $order = Order::find($validatedData['id']);
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
        $order->update($validatedData);
        return response(["message"=>"Order is updated", 'order'=>$order],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return response(["message"=>"Order is created", 'order'=>$order],201);
    }
}
