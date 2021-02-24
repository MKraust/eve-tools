<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCachedAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cached_assets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('character_id');
            $table->boolean('is_blueprint_copy')->default(0);
            $table->boolean('is_singleton');
            $table->bigInteger('item_id');
            $table->string('location_flag');
            $table->bigInteger('location_id');
            $table->string('location_type');
            $table->integer('quantity');
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
        Schema::dropIfExists('cached_assets');
    }
}
