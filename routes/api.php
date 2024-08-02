<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('inventory', InventoryController::class);
Route::apiResource('product', ProductController::class);
Route::apiResource('category', CategoryController::class);
Route::apiResource('warehouse', WarehouseController::class);

Route::apiResource('order', OrderController::class);
Route::apiResource('order-detail', OrderDetailController::class);
Route::apiResource('transaction', TransactionController::class);
Route::apiResource('return-order', ReturnOrderController::class);
Route::apiResource('vehicle', VehicleController::class);
Route::apiResource('shipment', ShipmentController::class);
//Route::get('/inventory', [InventoryController::class, 'index']);
//Route::post('/inventory', [InventoryController::class, 'store']);
//Route::get('/inventory/{inventory}', [InventoryController::class, 'show']);
//Route::put('/inventory/{inventory}', [InventoryController::class, 'update']);
//Route::patch('/inventory/{inventory}', [InventoryController::class, 'partialUpdate']);
//Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy']);


