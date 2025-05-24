<?php

Route::group(['prefix' => 'services'], function () {

    Route::get('index', 'WebService\ServiceController@getServices');
    Route::get('{id}/details', 'WebService\ServiceController@getServiceDetails');
    Route::get('categories', 'WebService\ServiceController@getServiceCategories')->name('api.catalog.service_categories');

    Route::group(['prefix' => 'orders'], function () {

        Route::post('create', 'WebService\ServiceOrderController@createOrder')->name('api.service_orders.create');
        Route::get('list', 'WebService\ServiceOrderController@userOrdersList')->name('api.service_orders.index');
        Route::get('{id}/details', 'WebService\ServiceOrderController@getOrderDetails')->name('api.service_orders.details');

    });
});
