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
        Schema::create('shipments', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('date');
            $table->string('status');
            $table->string('capacity');
            $table->dateTime('estimated_arrival_time');
            $table->dateTime('arrival_time');
            $table->dateTime('origin_address');
            $table->dateTime('destination_address');
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
        Schema::dropIfExists('shipments');
    }
};
