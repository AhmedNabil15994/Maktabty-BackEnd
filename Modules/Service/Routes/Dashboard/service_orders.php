<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'service-orders'], function () {

    Route::get('/', 'Dashboard\ServiceOrderController@index')
        ->name('dashboard.service_orders.index')
        ->middleware(['permission:show_service_orders']);

    Route::get('datatable', 'Dashboard\ServiceOrderController@datatable')
        ->name('dashboard.service_orders.datatable')
        ->middleware(['permission:show_service_orders']);

    Route::get('{id}', 'Dashboard\ServiceOrderController@show')
        ->name('dashboard.service_orders.show')
        ->middleware(['permission:show_service_orders']);

    Route::delete('{id}', 'Dashboard\ServiceOrderController@destroy')
        ->name('dashboard.service_orders.destroy')
        ->middleware(['permission:delete_service_orders']);

    Route::get('deletes', 'Dashboard\ServiceOrderController@deletes')
        ->name('dashboard.service_orders.deletes')
        ->middleware(['permission:delete_service_orders']);

});
