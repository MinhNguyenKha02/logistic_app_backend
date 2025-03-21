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
        Schema::create('return_orders', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('customer_id');
            $table->foreign('customer_id')->references('id')->on('users')
            ->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('product_id');
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('date');
            $table->string('reason');
            $table->string('status');
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
        Schema::dropIfExists('return_orders');
    }
};
