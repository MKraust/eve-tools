<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedContractItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_contract_items', function (Blueprint $table) {
            $table->bigInteger('record_id')->unique()->primary();
            $table->integer('contract_id');
            $table->boolean('is_included');
            $table->boolean('is_singleton');
            $table->integer('quantity');
            $table->integer('raw_quantity')->nullable();
            $table->integer('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_contract_items');
    }
}
