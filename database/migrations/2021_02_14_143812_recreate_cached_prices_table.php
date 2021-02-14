<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateCachedPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cached_prices');
        Schema::create('cached_prices', function (Blueprint $table) {
            $table->unsignedInteger('type_id')->primary()->unique();
            $table->unsignedDecimal('average', 20, 2)->nullable();
            $table->unsignedDecimal('adjusted', 20, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cached_prices');
        Schema::create('cached_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_id')->unique();
            $table->string('jita')->nullable();
            $table->string('dichstar')->nullable();
            $table->timestamps();
            $table->string('average')->nullable();
            $table->string('adjusted')->nullable();
            $table->string('monthly_volume')->nullable();
            $table->string('weekly_volume')->nullable();
            $table->string('average_daily_volume')->nullable();
        });
    }
}
