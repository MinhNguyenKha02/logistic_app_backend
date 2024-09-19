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
use App\Http\Controllers\UserController;

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


Route::post("login", [UserController::class, 'login']);
ROute::get("/users", [UserController::class, 'users']);
Route::middleware("auth:api")->group(function () {
    Route::get("/send", [UserController::class, 'index']);
    ROute::post("/logout", [UserController::class, 'logout']);
    ROute::get("/current-user", [UserController::class, 'getCurrentUser']);
    ROute::get("/session-value", [UserController::class, 'getSessionData']);
});

//Route::get("/test", [UserController::class, 'index']);

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::apiResource('inventory', InventoryController::class);
Route::get('inventory-unit', [InventoryController::class, 'units']);
Route::get("search-inventories", [InventoryController::class, 'search']);
Route::get("order-inventories", [InventoryController::class, 'order']);
Route::apiResource('products', ProductController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('warehouses', WarehouseController::class);

Route::apiResource('orders', OrderController::class);
Route::get('orders-status', [OrderController::class, 'status']);
Route::apiResource('orders-detail', OrderDetailController::class);
Route::apiResource('transactions', TransactionController::class);
Route::get('transactions-status', [TransactionController::class, 'status']);
Route::get('transaction-latest', [TransactionController::class, 'getLatestTransaction']);
Route::get('transactions-type', [TransactionController::class, 'types']);
Route::get('transactions-unit', [TransactionController::class, 'units']);
Route::apiResource('return-orders', ReturnOrderController::class);
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('shipments', ShipmentController::class);
Route::get("search-shipments", [ShipmentController::class, 'search']);
Route::get("order-shipments", [ShipmentController::class, 'order']);
Route::get('shipments-status', [ShipmentController::class,'status']);
Route::get('shipment-latest', [ShipmentController::class,'getLatestShipment']);
Route::get("newest-inventory-id", [InventoryController::class, 'newestInventoryId']);

Route::get("search", [InventoryController::class, 'search']);

//Route::get('/inventory', [InventoryController::class, 'index']);
//Route::post('/inventory', [InventoryController::class, 'store']);
//Route::get('/inventory/{inventory}', [InventoryController::class, 'show']);
//Route::put('/inventory/{inventory}', [InventoryController::class, 'update']);
//Route::patch('/inventory/{inventory}', [InventoryController::class, 'partialUpdate']);
//Route::delete('/inventory/{inventory}', [InventoryController::class, 'destroy']);


