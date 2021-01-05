<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionTrackedTypeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_tracked_type_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('produced')->default(0);
            $table->integer('invented')->default(0);
            $table->timestamps();

            $table->unsignedBigInteger('tracked_type_id');
            $table->foreign('tracked_type_id')->references('id')->on('production_tracked_types')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_tracked_type_logs');
    }
}
