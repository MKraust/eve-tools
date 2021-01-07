<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('duration');
            $table->boolean('is_buy_order');
            $table->dateTime('issued');
            $table->unsignedBigInteger('location_id');
            $table->integer('min_volume');
            $table->bigInteger('order_id');
            $table->string('price');
            $table->string('range');
            $table->integer('system_id')->nullable();
            $table->integer('type_id');
            $table->integer('volume_remain');
            $table->integer('volume_total');
            $table->timestamps();

            $table->index(['type_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_orders');
    }
}
