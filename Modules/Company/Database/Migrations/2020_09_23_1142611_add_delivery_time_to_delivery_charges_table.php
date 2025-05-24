<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeliveryTimeToDeliveryChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_charges', function (Blueprint $table) {
            $table->integer('delivery_time')->default(60)->after('delivery');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_charges', function (Blueprint $table) {
            $table->dropColumn('delivery_time');
        });
    }
}
