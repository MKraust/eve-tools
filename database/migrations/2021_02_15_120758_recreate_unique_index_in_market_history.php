<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RecreateUniqueIndexInMarketHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cached_orders_history')->truncate();

        Schema::table('cached_orders_history', function (Blueprint $table) {
            $table->dropUnique(['type_id', 'date']);
            $table->unique(['type_id', 'region_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cached_orders_history', function (Blueprint $table) {
            $table->dropUnique(['type_id', 'region_id', 'date']);
            $table->unique(['type_id', 'date']);
        });
    }
}
