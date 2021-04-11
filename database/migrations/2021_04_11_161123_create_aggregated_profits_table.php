<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedProfitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregated_profits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_id');
            $table->integer('quantity');
            $table->dateTime('date');
            $table->double('margin', 20);
            $table->double('delivery_cost', 20);
            $table->double('buy_broker_fee', 20);
            $table->double('sell_broker_fee', 20);
            $table->double('sales_tax', 20);
            $table->double('profit', 20);

            $table->unsignedBigInteger('buy_transaction_id');
            $table->unsignedBigInteger('sell_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregated_profits');
    }
}
