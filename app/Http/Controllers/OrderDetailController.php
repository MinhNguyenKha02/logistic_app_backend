<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderDetailRequest;
use App\Http\Requests\UpdateOrderDetailRequest;
use App\Models\Order;
use App\Models\OrderDetail;

class OrderDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(["orders_detail" => OrderDetail::all()], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreOrderDetailRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderDetailRequest $request)
    {
        $validatedData = $request->validated();
        $order_id = $validatedData['order_id'];
        $order = Order::find($order_id);
        if(!$order->orderDetail){
            OrderDetail::create($validatedData);
            $orderDetail = OrderDetail::find($validatedData['id']);
            return response(["message" => "Order detail is created", "order_detail"=>$orderDetail], 200);
        }else{
            return response(["message" => "Order detail is already created, check your order_id"], 400);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function show(OrderDetail $orderDetail)
    {
        return response(["order_detail" => $orderDetail], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderDetailRequest  $request
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderDetailRequest $request, OrderDetail $orderDetail)
    {
        $validatedData = $request->validated();
        $orderDetail->update($validatedData);
        return response(["message" => "Order detail is updated", "order_detail"=>$orderDetail], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderDetail  $orderDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderDetail $orderDetail)
    {
        $orderDetail->delete();
        return response(["message" => "Order detail is deleted"], 200);
    }
}
