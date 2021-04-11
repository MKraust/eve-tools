<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedQuantityToCachedTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cached_transactions', function (Blueprint $table) {
            $table->integer('processed_quantity')->default(0);
        });

        Schema::table('characters', function (Blueprint $table) {
            $table->double('buy_broker_fee_percent')->default(0);
            $table->double('sell_broker_fee_percent')->default(0);
            $table->double('sales_tax_percent')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('characters', function (Blueprint $table) {
            $table->dropColumn('buy_broker_fee');
            $table->dropColumn('sell_broker_fee');
            $table->dropColumn('sales_tax');
        });

        Schema::table('cached_transactions', function (Blueprint $table) {
            $table->dropColumn('processed_quantity');
        });
    }
}
