<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shipment','namespace' => 'Dashboard'], function () {

    Route::put('cancel/{orderId}', 'ShipmentController@cancel')
        ->name('dashboard.shipment.cancel')
        ->middleware(['permission:cancel_shipment_request']);
});
