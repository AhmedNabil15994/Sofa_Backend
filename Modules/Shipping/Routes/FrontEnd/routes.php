<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shipment','namespace' => 'Frontend'], function () {

    Route::post('calculate-rate', 'ShipmentController@calculateRate')
        ->name('frontend.shipping.calculate.rate');
});
