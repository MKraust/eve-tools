<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatedVolumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregated_volumes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('region_id');
            $table->unsignedBigInteger('monthly')->default(0);
            $table->unsignedBigInteger('weekly')->default(0);
            $table->unsignedDecimal('average_daily', 20, 2)->default(0);

            $table->index(['type_id', 'region_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aggregated_volumes');
    }
}
