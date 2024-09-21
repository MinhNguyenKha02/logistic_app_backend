<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReturnOrder;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $fromWeekDate = Carbon::now()->subWeek();

        $ordersPerDay = collect();
        $returnOrdersPerDay = collect();
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $ordersCount = Order::whereDate('created_at', '=', $date)->count();
            $ordersPerDay->push($ordersCount);
        }

        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $returnOrdersCount = ReturnOrder::whereDate('created_at', '=', $date)->count();
            $returnOrdersPerDay->push($returnOrdersCount);
        }

        $statistics = [
            'users' => User::where('created_at', '>=', $fromWeekDate)->count(),
            'inventories' => Inventory::where('created_at', '>=', $fromWeekDate)->count(),
            'products' => Product::where('created_at', '>=', $fromWeekDate)->count(),
            'orders' => Order::where('created_at', '>=', $fromWeekDate)->count(),
            'return_orders' => ReturnOrder::where('created_at', '>=', $fromWeekDate)->count(),
            'transactions' => Transaction::where('created_at', '>=', $fromWeekDate)->count(),
            'revenue' => Receipt::where('updated_at', '>=', $fromWeekDate)->sum('price'),
            "ordersPerDay" => $ordersPerDay,
            "returnOrdersPerDay" => $returnOrdersPerDay,
        ];

        return view('emails.my-report')->with('statistics',$statistics);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function show(Drug $drug)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Drug $drug)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Drug  $drug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Drug $drug)
    {
        //
    }
}
