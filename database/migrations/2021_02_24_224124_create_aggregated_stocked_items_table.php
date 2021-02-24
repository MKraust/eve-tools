<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedStockedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregated_stocked_items', function (Blueprint $table) {
            $table->id();
            $table->integer('character_id');
            $table->integer('type_id');
            $table->bigInteger('location_id');
            $table->bigInteger('quantity');

            $table->unique(['character_id', 'type_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregated_stocked_items');
    }
}
