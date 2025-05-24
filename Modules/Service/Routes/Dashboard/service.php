<?php

Route::group(['prefix' => 'services'], function () {

    Route::get('/', 'Dashboard\ServiceController@index')
        ->name('dashboard.services.index')
        ->middleware(['permission:show_services']);

    Route::get('datatable', 'Dashboard\ServiceController@datatable')
        ->name('dashboard.services.datatable')
        ->middleware(['permission:show_services']);

    Route::get('create', 'Dashboard\ServiceController@create')
        ->name('dashboard.services.create')
        ->middleware(['permission:add_services']);

    Route::post('/', 'Dashboard\ServiceController@store')
        ->name('dashboard.services.store')
        ->middleware(['permission:add_services']);

    Route::get('{id}/edit', 'Dashboard\ServiceController@edit')
        ->name('dashboard.services.edit')
        ->middleware(['permission:edit_services']);

    Route::put('{id}', 'Dashboard\ServiceController@update')
        ->name('dashboard.services.update')
        ->middleware(['permission:edit_services']);

    Route::delete('{id}', 'Dashboard\ServiceController@destroy')
        ->name('dashboard.services.destroy')
        ->middleware(['permission:delete_services']);

    Route::get('deletes', 'Dashboard\ServiceController@deletes')
        ->name('dashboard.services.deletes')
        ->middleware(['permission:delete_services']);

    Route::get('{id}', 'Dashboard\ServiceController@show')
        ->name('dashboard.services.show')
        ->middleware(['permission:show_services']);

});
