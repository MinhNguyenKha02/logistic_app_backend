<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('return_orders_shipments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('return_order_id')->references('id')->on('return_orders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('shipment_id')->references('id')->on('shipments')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('return_orders_shipments');
    }
};
