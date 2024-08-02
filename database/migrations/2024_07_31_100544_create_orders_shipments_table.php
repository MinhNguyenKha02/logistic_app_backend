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
        Schema::create('orders_shipments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('order_id');
            $table->string('shipment_id');

            $table->foreign('order_id')->references('id')->on('orders')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('shipment_id')->references('id')->on('shipments')
                ->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('orders_shipments');
    }
};
