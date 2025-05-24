<?php

Route::group(['prefix' => 'orders'], function () {

    Route::post('create', 'WebService\OrderController@createOrder')->name('api.orders.create');

    Route::get('success-tap', 'WebService\OrderController@successTap')
        ->name('api.orders.success.tap');

    Route::get('success-upayemnt', 'WebService\OrderController@successUpayment')
        ->name('api.orders.success.upayemnt');

    Route::get('myfatoorah-callback', 'WebService\OrderController@myFatoorahCallBack')
        ->name('api.orders.myfatoorah.callback');

    Route::get('success', 'WebService\OrderController@success')
        ->name('api.orders.success');

    Route::get('failed', 'WebService\OrderController@failed')
        ->name('api.orders.failed');

    Route::get('payment-methods', 'WebService\OrderController@paymentMethods')
        ->name('api.orders.payment.methods');

    Route::get('list', 'WebService\OrderController@userOrdersList')->name('api.orders.index');
    Route::get('{id}/details', 'WebService\OrderController@getOrderDetails')->name('api.orders.details');
    
    Route::post('{id}/cancel', 'WebService\OrderController@cancelOrderPayment')->name('api.orders.cancel');
});
