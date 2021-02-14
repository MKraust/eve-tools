<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedCharacterOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregated_character_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('order_id')->primary()->unique();
            $table->unsignedBigInteger('character_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedDecimal('price', 20, 2);
            $table->integer('type_id');
            $table->integer('volume_remain');
            $table->integer('volume_total');
            $table->unsignedDecimal('outbid', 20, 2)->nullable();

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
        Schema::dropIfExists('aggregated_character_orders');
    }
}
