<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

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

Route::post("register", [UserController::class, "register"]);
Route::post("login", [UserController::class, 'login']);
Route::middleware("auth:api")->middleware(\App\Http\Middleware\UpdateLastActive::class)->group(function () {
    Route::get("users-all", [UserController::class, 'users']);
    Route::get("shipment-latest", [ShipmentController::class, 'getLatestShipment']);
    Route::apiResource("users", UserController::class);
    Route::get("search-users", [UserController::class, 'search']);
    Route::get("order-users", [UserController::class, 'order']);
    Route::get("roles", [UserController::class, 'role']);
    Route::middleware(\App\Http\Middleware\UpdateLogoutActivity::class)->post("/logout", [UserController::class, 'logout']);
    Route::get("/current-user", [UserController::class, 'getCurrentUser']);
    Route::get("/session-value", [UserController::class, 'getSessionData']);
    Route::get("/online-users",[UserController::class, 'getOnlineUsers']);

    Route::post("/send-message", [\App\Http\Controllers\ConversationController::class, 'sendMessage']);
    Route::get("/get-messages", [\App\Http\Controllers\ConversationController::class, 'getMessages']);


    Route::apiResource('inventory', InventoryController::class);
    Route::get('inventory-unit', [InventoryController::class, 'units']);
    Route::get("search-inventories", [InventoryController::class, 'search']);
    Route::get("order-inventories", [InventoryController::class, 'order']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::get("/latest-order", [OrderController::class, 'latestOrder']);
    Route::post("/update-status", [\App\Http\Controllers\ConversationController::class, 'updateUserStatus']);
    Route::post("/conversations",[\App\Http\Controllers\ConversationController::class, 'getOrCreate']);
    Route::apiResource('receipts', ReceiptController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('return-orders', ReturnOrderController::class);
    Route::get('search-orders', [OrderController::class, 'search']);
    Route::get('order-orders', [OrderController::class, 'order']);
    Route::get('search-return-orders', [ReturnOrderController::class, 'search']);
    Route::get('order-return-orders', [ReturnOrderController::class, 'order']);
    Route::get("/latest-order-by-current-user", [OrderController::class,'getLatestOrderByCurrentUser']);
    Route::get("/latest-return-order-by-current-user", [ReturnOrderController::class,'getLatestReturnOrderByCurrentUser']);
    Route::get('orders-status', [OrderController::class, 'status']);
    Route::apiResource('orders-detail', OrderDetailController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::get('search-transactions', [TransactionController::class, 'search']);
    Route::get('transactions-status', [TransactionController::class, 'status']);
    Route::get('transaction-latest', [TransactionController::class, 'getLatestTransaction']);
    Route::get('transactions-type', [TransactionController::class, 'types']);
    Route::get('transactions-unit', [TransactionController::class, 'units']);
    Route::apiResource('return-orders', ReturnOrderController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('shipments', ShipmentController::class);
    Route::post('store-order-shipment', [ShipmentController::class, 'storeOrderShipment']);
    Route::post('store-return-order-shipment', [ShipmentController::class, 'storeReturnOrderShipment']);
    Route::get("search-shipments", [ShipmentController::class, 'search']);
    Route::get("order-shipments", [ShipmentController::class, 'order']);
    Route::get('shipments-status', [ShipmentController::class,'status']);
    Route::get('shipment-latest', [ShipmentController::class,'getLatestShipment']);
    Route::get("newest-inventory-id", [InventoryController::class, 'newestInventoryId']);
    Route::get("otp", [UserController::class, 'otp']);
    Route::post('confirm-otp', [UserController::class, 'confirmOTP']);
    Route::post('breakdown', [ShipmentController::class, 'breakdown']);
    Route::post('add-breakdown-shipment', [ShipmentController::class, 'addBreakDownShipment']);
    Route::get('get-order-breakdown', [OrderController::class, 'fetchOrderBreakdown']);
    Route::get("search", [InventoryController::class, 'search']);

    Route::get("payment", [PaymentController::class, 'payment']);
    Route::get("vnpay-callback", [PaymentController::class, 'payCallBack']);

    Route::get("get-notifications-by-current-user", [\App\Http\Controllers\ConversationController::class, 'getNotificationsByCurrentUser']);
    Route::patch("mark-as-read", [\App\Http\Controllers\ConversationController::class, 'markAsRead']);

    Route::get("/user-quantity", [\App\Http\Controllers\StatisticController::class, 'userQuantity']);
    Route::get("/inventory-quantity", [\App\Http\Controllers\StatisticController::class, 'inventoryQuantity']);
    Route::get("/product-quantity", [\App\Http\Controllers\StatisticController::class, 'productQuantity']);
    Route::get("/order-quantity", [\App\Http\Controllers\StatisticController::class, 'orderQuantity']);
    Route::get("/return-order-quantity", [\App\Http\Controllers\StatisticController::class, 'returnOrderQuantity']);
    Route::get("/transaction-quantity", [\App\Http\Controllers\StatisticController::class, 'transactionQuantity']);
    Route::get("/revenue", [\App\Http\Controllers\StatisticController::class, 'revenue']);
    Route::get("/statistics-each-week", [\App\Http\Controllers\StatisticController::class, 'ordersEachDays']);
    Route::get("/revenue-each-week", [\App\Http\Controllers\StatisticController::class, 'revenueEachWeek']);

    Route::get("/orders-by-current-user", [OrderController::class, 'getOrdersByCurrentUser']);
    Route::get("/return-orders-by-current-user", [ReturnOrderController::class, 'getReturnOrdersByCurrentUser']);
    Route::get("/order-by-id", [OrderController::class, 'getOrderById']);
    Route::get("/return-order-by-id", [ReturnOrderController::class, 'getReturnOrderById']);
    Route::patch("/shipment-delivery-by-id/{shipment}", [ShipmentController::class, 'shipmentDetailDelivery']);
    Route::patch("/order-delivery-by-id/{order}", [OrderController::class, 'updateOrderDelivery']);
    Route::patch("/return-order-delivery-by-id/{order}", [ReturnOrderController::class, 'updateReturnOrderDelivery']);

    Route::patch("/confirm-return-order/{order}", [ReturnOrderController::class, 'confirmReturnOrder']);
    Route::patch("/confirm-order/{order}", [OrderController::class, 'confirmOrder']);

});






