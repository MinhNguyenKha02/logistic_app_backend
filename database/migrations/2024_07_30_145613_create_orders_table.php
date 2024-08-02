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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('product_id');
            $table->foreign('product_id')->references('id')->on('products')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('transaction_id');
            $table->foreign('transaction_id')->references('id')->on('transactions')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->dateTime('date');
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
        Schema::dropIfExists('orders');
    }
};
