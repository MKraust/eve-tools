<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_contracts', function (Blueprint $table) {
            $table->integer('contract_id')->unique()->primary();
            $table->integer('character_id');
            $table->integer('acceptor_id');
            $table->integer('assignee_id');
            $table->string('availability');
            $table->decimal('buyout', 20, 2)->nullable();
            $table->decimal('collateral', 20, 2)->nullable();
            $table->dateTime('date_accepted')->nullable();
            $table->dateTime('date_completed')->nullable();
            $table->dateTime('date_expired');
            $table->dateTime('date_issued');
            $table->integer('days_to_complete')->nullable();
            $table->bigInteger('end_location_id')->nullable();
            $table->boolean('for_corporation');
            $table->integer('issuer_corporation_id');
            $table->integer('issuer_id');
            $table->decimal('price', 20, 2)->nullable();
            $table->decimal('reward', 20, 2)->nullable();
            $table->bigInteger('start_location_id')->nullable();
            $table->string('status');
            $table->string('title')->nullable();
            $table->string('type');
            $table->decimal('volume', 20, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_contracts');
    }
}
