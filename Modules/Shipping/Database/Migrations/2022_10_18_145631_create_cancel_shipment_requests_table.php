<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCancelShipmentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancel_shipment_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shipment_id');
            $table->string('status');
            $table->longText('reason');
            $table->json('response');
            $table->string('type')->default('local');
            $table->bigInteger('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancel_shipment_requests');
    }
}
