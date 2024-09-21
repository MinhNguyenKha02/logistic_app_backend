<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\ReturnOrder;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    protected $validTimeOptions = ['week', 'month', 'year'];

    protected function getValidatedTime(Request $request)
    {
        $time = $request->get('time');
        return in_array($time, $this->validTimeOptions) ? $time : 'week'; // Default to 'week' if invalid
    }
    public function calculateCountByTime($model, $time)
    {
        switch ($time) {
            case 'week':
                $fromDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $fromDate = Carbon::now()->subMonth();
                break;
            case 'year':
                $fromDate = Carbon::now()->subYear();
                break;
            default:
                $fromDate = Carbon::now()->subWeek();
        }

        // Query the count of records created within the time range
        return $model::where('created_at', '>=', $fromDate)->count();
    }

    public function revenueEachWeek()
    {
        $receipts = \App\Models\Receipt::all();
        $revenuesData = [0, 0, 0, 0, 0, 0, 0];
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($receipts as $receipt) {
            $dayOfWeek = \Carbon\Carbon::parse($receipt->created_at)->dayOfWeek;

            $revenuesData[$dayOfWeek] += $receipt->price;
        }
        return response([
            'days' => $days,
            'revenues' => $revenuesData,
        ],200);
    }

    public function ordersEachDays()
    {
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
        return response([
            "ordersPerDay" => $ordersPerDay,
            "returnOrdersPerDay" => $returnOrdersPerDay,
            'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        ],200);
    }

    public function userQuantity(Request $request){
        $time = $this->getValidatedTime($request);
        // User model count by time
        $userCount = $this->calculateCountByTime(User::class, $time);
        return response()->json([
            'time' => $time,
            'user_count' => $userCount,
        ]);
    }
    public function inventoryQuantity(Request $request){
        $time = $this->getValidatedTime($request);
        $inventoryCount = $this->calculateCountByTime(Inventory::class, $time);
        return response()->json([
            'time' => $time,
            'inventory_count' => $inventoryCount,
        ]);
    }

    public function productQuantity(Request $request){
        $time = $this->getValidatedTime($request);
        $productCount = $this->calculateCountByTime(Product::class, $time);
        return response()->json([
            'time' => $time,
            'product_count' => $productCount,
        ]);
    }

    public function orderQuantity(Request $request){
        $time = $this->getValidatedTime($request);
        $orderCount = $this->calculateCountByTime(Order::class, $time);
        return response()->json([
            'time' => $time,
            'order_count' => $orderCount,
        ]);
    }

    public function returnOrderQuantity(Request $request){
        $time = $this->getValidatedTime($request);
        $returnOrderCount = $this->calculateCountByTime(ReturnOrder::class, $time);
        return response()->json([
            'time' => $time,
            'return_order_count' => $returnOrderCount,
        ]);
    }

    public function transactionQuantity(Request $request)
    {
        $time = $this->getValidatedTime($request);
        $transactionCount = $this->calculateCountByTime(Transaction::class, $time);
        return response()->json([
            'time' => $time,
            'transaction_count' => $transactionCount,
        ]);
    }
    public function revenue(Request $request){
        $time = $this->getValidatedTime($request);
        switch ($time) {
            case 'week':
                $fromDate = Carbon::now()->subWeek();
                break;
            case 'month':
                $fromDate = Carbon::now()->subMonth();
                break;
            case 'year':
                $fromDate = Carbon::now()->subYear();
                break;
            default:
                $fromDate = Carbon::now()->subWeek();
        }
        $totalRevenue = Receipt::where('updated_at', '>=', $fromDate)->sum('price');
        return response()->json([
            'time' => $time,
            'total_revenue' => $totalRevenue,
        ]);
    }
}
