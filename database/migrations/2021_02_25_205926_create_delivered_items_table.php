<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveredItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivered_items', function (Blueprint $table) {
            $table->id();
            $table->integer('type_id');
            $table->integer('quantity');
            $table->bigInteger('destination_id');
            $table->dateTime('delivered_date')->nullable();
            $table->timestamps();

            $table->index(['type_id', 'delivered_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivered_items');
    }
}
