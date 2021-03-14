<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAggregatedStockedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aggregated_stocked_items', function (Blueprint $table) {
            $table->dropColumn('quantity');

            $table->bigInteger('in_hangar')->default(0);
            $table->bigInteger('in_market')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aggregated_stocked_items', function (Blueprint $table) {
            $table->dropColumn('in_hangar');
            $table->dropColumn('in_market');

            $table->bigInteger('quantity')->default(0);
        });
    }
}
