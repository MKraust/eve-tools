<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedOrdersHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_orders_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_id');
            $table->date('date');
            $table->double('average');
            $table->double('highest');
            $table->double('lowest');
            $table->integer('order_count');
            $table->integer('volume');

            $table->index('type_id');
            $table->index(['type_id', 'date']);
            $table->unique(['type_id', 'date']);
        });

        Schema::table('cached_prices', function (Blueprint $table) {
            $table->string('monthly_volume')->nullable();
            $table->string('weekly_volume')->nullable();
            $table->string('average_daily_volume')->nullable();
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
            $table->dropColumn('monthly_volume');
            $table->dropColumn('weekly_volume');
            $table->dropColumn('average_daily_volume');
        });

        Schema::dropIfExists('cached_orders_history');
    }
}
