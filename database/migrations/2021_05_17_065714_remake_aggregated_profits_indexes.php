<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemakeAggregatedProfitsIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aggregated_profits', function (Blueprint $table) {
            $table->string('buy_transaction_type')->default('cached');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aggregated_profits', function (Blueprint $table) {
            $table->dropColumn('buy_transaction_type');
        });
    }
}
