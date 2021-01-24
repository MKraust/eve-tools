<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsMyOrderColumnToCachedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_orders', function (Blueprint $table) {
            $table->boolean('is_my_order')->default(false);

            $table->index(['is_my_order', 'type_id']);
        });

        Schema::table('cached_prices', function (Blueprint $table) {
            $table->string('adjusted_daily_volume')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cached_prices', function (Blueprint $table) {
            $table->dropColumn('adjusted_daily_volume');
        });

        Schema::table('cached_orders', function (Blueprint $table) {
            $table->dropIndex(['is_my_order', 'type_id']);

            $table->dropColumn('is_my_order');
        });
    }
}
