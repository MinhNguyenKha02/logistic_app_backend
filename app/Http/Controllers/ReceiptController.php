<?php

namespace App\Http\Controllers;

use App\Events\createOrder;
use App\Models\Receipt;
use App\Notifications\MessageOrderSampleNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'order_id' => 'required',
            'status' => 'required',
            'price'=>'required|numeric',
        ]);
        $validatedData['id']=fake()->uuid();
        $receipt = Receipt::create($validatedData);
        broadcast(new createOrder())->toOthers();
        return response()->json(["message"=>"Create successfully receipt", "receipt"=>$receipt], 201);
    }

    public function index(Request $request)
    {
        $directions = ["asc", "desc"];
        $type = $request['type'];
        $direction = $request['direction'];
        if($direction==""||!$direction){
            $shipments = Receipt::query()->paginate(3);
            return response(["receipts"=>$shipments], 200);
        }else{
            $shipments = Receipt::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "receipts" => $shipments
            ], 200);
        }
    }


    public function show(Receipt $receipt)
    {
        return response(["receipt"=>$receipt],200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipt $receipt)
    {
        $validatedData = $request->validated();
        $receipt->update($validatedData);
        return response(["message"=>"Receipt is updated", 'receipt'=>$receipt],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipt $receipt)
    {
        $receipt->delete();
        return response(["message"=>"Receipt is deleted"],204);
    }
}
