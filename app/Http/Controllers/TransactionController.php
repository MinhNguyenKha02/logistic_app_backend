<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\Unit;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Inventory;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function search(Request $request){
        $keyword = $request['keyword'];
        $transaction = Transaction::query()->where("id", "like", "%$keyword%")
            ->orWhere("product_id", "like", "%$keyword%")
            ->orWhere("unit", "like", "%$keyword%")
            ->orWhere("type", "like", "%$keyword%")
            ->orwhere("quantity", "like", "%$keyword%")
            ->orWhere("date", "like", "%$keyword%")
            ->orWhere("status", "like", "%$keyword%")
            ->orWhere("created_at", "like", "%$keyword%")
            ->orWhere("updated_at", "like", "%$keyword%")
            ->orWhereHas('product', function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%");
            })
            ->paginate(3);
        if($transaction){
            return response(["transactions"=>$transaction], 200);
        }
    }
    public function units()
    {
        return response(["units"=>Unit::cases()], 200);
    }
    public function status(){
        return response(["statuss"=>TransactionStatus::cases()],200);
    }
    public function types(){
        return response(["types"=>TransactionType::cases()],200);
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
            $transaction = Transaction::query()->paginate(3);
            return response(["transactions"=>$transaction], 200);
        }else{
            $transaction = Transaction::query()->orderBy($type, $direction)->paginate(3);

            return response([
                "transactions" => $transaction
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTransactionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTransactionRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['id'] = fake()->uuid();
        Transaction::create($validatedData);
        $transactions = Transaction::find($validatedData['id']);
        $inventory = Inventory::query()->where('id',$transactions->product->inventory->id)->first();
        if ($inventory) {
            $inventory->update(['quantity' => (int) $inventory->quantity + (int) $validatedData['quantity']]);
            $inventory->save();
        }
        return response(["message"=>"Transaction is created",'transaction'=>$transactions],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return response(['transaction'=>$transaction], 200);
    }

    public function getLatestTransaction(){
        $transaction = Transaction::query()->latest()->first();
        return response(['transaction'=>$transaction],200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTransactionRequest  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $validatedData = $request->validated();
        if(isset($validatedData['quantity'])){
            $inventory = Inventory::query()->where('id',$transaction->product->inventory->id)->first();
            if ($inventory) {
                $inventory->update(['quantity'=>(int) $transaction->product->inventory->quantity - (int) $transaction->quantity]);
                $inventory->update(['quantity'=> (int) $transaction->product->inventory->quantity+ (int) $validatedData['quantity']]);
                $inventory->save();
            }
        }
        $transaction->update($validatedData);
        return response(['transaction'=>$transaction], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response(['message'=>"Transaction is deleted"], 204);
    }
}
