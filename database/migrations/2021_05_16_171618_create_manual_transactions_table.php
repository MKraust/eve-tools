<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManualTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_transactions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->useCurrent();
            $table->boolean('is_buy');
            $table->bigInteger('location_id');
            $table->integer('quantity');
            $table->integer('type_id');
            $table->double('unit_price');
            $table->integer('processed_quantity')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_transactions');
    }
}
