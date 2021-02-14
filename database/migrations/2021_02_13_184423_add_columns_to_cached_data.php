<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCachedData extends Migration
{
    public function up()
    {
        Schema::table('cached_transactions', function (Blueprint $table) {
            $table->bigInteger('character_id')->nullable();
        });

        Schema::table('cached_orders_history', function (Blueprint $table) {
            $table->bigInteger('region_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cached_orders_history', function (Blueprint $table) {
            $table->bigInteger('region_id')->nullable();
        });

        Schema::table('cached_transactions', function (Blueprint $table) {
            $table->dropColumn('character_id');
        });
    }
}
