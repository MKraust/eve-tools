<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->dateTime('date');
            $table->boolean('is_buy');
            $table->boolean('is_personal');
            $table->bigInteger('journal_ref_id');
            $table->bigInteger('location_id');
            $table->integer('quantity');
            $table->bigInteger('transaction_id')->unique();
            $table->integer('type_id');
            $table->double('unit_price');

            $table->index('location_id');
            $table->index('date');
            $table->index(['type_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_transactions');
    }
}
