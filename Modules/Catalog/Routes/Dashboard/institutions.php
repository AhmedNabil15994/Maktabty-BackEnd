<?php

Route::group(['prefix' => 'educational-institutions'], function () {

    Route::get('/', 'Dashboard\EducationalInstitutionController@index')
        ->name('dashboard.educational_institutions.index')
        ->middleware(['permission:show_educational_institutions']);

    Route::get('datatable', 'Dashboard\EducationalInstitutionController@datatable')
        ->name('dashboard.educational_institutions.datatable')
        ->middleware(['permission:show_educational_institutions']);

    Route::get('create', 'Dashboard\EducationalInstitutionController@create')
        ->name('dashboard.educational_institutions.create')
        ->middleware(['permission:add_educational_institutions']);

    Route::post('/', 'Dashboard\EducationalInstitutionController@store')
        ->name('dashboard.educational_institutions.store')
        ->middleware(['permission:add_educational_institutions']);

    Route::get('{id}/edit', 'Dashboard\EducationalInstitutionController@edit')
        ->name('dashboard.educational_institutions.edit')
        ->middleware(['permission:edit_educational_institutions']);

    Route::put('{id}', 'Dashboard\EducationalInstitutionController@update')
        ->name('dashboard.educational_institutions.update')
        ->middleware(['permission:edit_educational_institutions']);

    Route::delete('{id}', 'Dashboard\EducationalInstitutionController@destroy')
        ->name('dashboard.educational_institutions.destroy')
        ->middleware(['permission:delete_educational_institutions']);

    Route::get('deletes', 'Dashboard\EducationalInstitutionController@deletes')
        ->name('dashboard.educational_institutions.deletes')
        ->middleware(['permission:delete_educational_institutions']);

    Route::get('{id}', 'Dashboard\EducationalInstitutionController@show')
        ->name('dashboard.educational_institutions.show')
        ->middleware(['permission:show_educational_institutions']);

});
