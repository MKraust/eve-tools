<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('destination_id');
            $table->dateTime('finished_at')->nullable();
            $table->timestamps();
        });

        Schema::table('delivered_items', function (Blueprint $table) {
            $table->dropIndex(['type_id', 'delivered_date']);

            $table->dropColumn('delivered_date');
            $table->dropColumn('destination_id');

            $table->unsignedDecimal('volume', 20, 2);
            $table->foreignId('delivery_id')->nullable()->constrained()->cascadeOnDelete();

            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivered_items', function (Blueprint $table) {
            $table->dropIndex(['type_id']);

            $table->bigInteger('destination_id');
            $table->dateTime('delivered_date')->nullable();

            $table->dropColumn('volume');
            $table->dropConstrainedForeignId('delivery_id');

            $table->index(['type_id', 'delivered_date']);
        });

        Schema::dropIfExists('deliveries');
    }
}
