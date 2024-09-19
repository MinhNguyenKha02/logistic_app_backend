<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Enums\Unit;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
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
    public function index()
    {
        return response(['transactions'=>Transaction::all()], 200);
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
        $validatedData['id'] = Transaction::newestTransactionId();
        Transaction::create($validatedData);
        $transactions = Transaction::find($validatedData['id']);
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
