<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregated_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedDecimal('sell')->nullable();
            $table->unsignedDecimal('buy')->nullable();

            $table->unique(['type_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregated_prices');
    }
}
