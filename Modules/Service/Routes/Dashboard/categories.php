<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'service-categories'], function () {

    Route::get('/', 'Dashboard\ServiceCategoryController@index')
        ->name('dashboard.service_categories.index')
        ->middleware(['permission:show_service_categories']);

    Route::get('datatable', 'Dashboard\ServiceCategoryController@datatable')
        ->name('dashboard.service_categories.datatable')
        ->middleware(['permission:show_service_categories']);

    Route::get('exports/{pdf}', 'Dashboard\ServiceCategoryController@export')
        ->name('dashboard.service_categories.export')
        ->middleware(['permission:show_service_categories']);

    Route::get('create', 'Dashboard\ServiceCategoryController@create')
        ->name('dashboard.service_categories.create')
        ->middleware(['permission:add_service_categories']);

    //import excel
    Route::post('import', 'Dashboard\ServiceCategoryController@Import')
        ->name('dashboard.service_categories.import.excel')
        ->middleware(['permission:add_service_categories']);

    Route::post('/', 'Dashboard\ServiceCategoryController@store')
        ->name('dashboard.service_categories.store')
        ->middleware(['permission:add_service_categories']);

    Route::get('{id}/edit', 'Dashboard\ServiceCategoryController@edit')
        ->name('dashboard.service_categories.edit')
        ->middleware(['permission:edit_service_categories']);

    Route::post('/update/photo', 'Dashboard\ServiceCategoryController@updatePhoto')
        ->name('dashboard.service_categories.update.photo')
        ->middleware(['permission:edit_service_categories']);

    Route::put('{id}', 'Dashboard\ServiceCategoryController@update')
        ->name('dashboard.service_categories.update')
        ->middleware(['permission:edit_service_categories']);

    Route::delete('{id}', 'Dashboard\ServiceCategoryController@destroy')
        ->name('dashboard.service_categories.destroy')
        ->middleware(['permission:delete_service_categories']);

    Route::get('deletes', 'Dashboard\ServiceCategoryController@deletes')
        ->name('dashboard.service_categories.deletes')
        ->middleware(['permission:delete_service_categories']);

    Route::get('{id}', 'Dashboard\ServiceCategoryController@show')
        ->name('dashboard.service_categories.show')
        ->middleware(['permission:show_service_categories']);

});
