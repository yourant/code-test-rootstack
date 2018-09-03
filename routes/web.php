<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('api_docs', 'DocsController@index')->name('api_docs.index');

// Backend
Route::group(['middleware' => 'sentinel.auth'], function () {
    // Sortings
    Route::resource('sortings', 'SortingsController');
    Route::put('sorting/{id}/unassign_service', 'SortingsController@unassignService')->name('sortings.unassign_service');
    Route::post('sorting/{id}/assign_service', 'SortingsController@assignService')->name('sortings.assign_service');

    // Sorting Gates
    Route::resource('sortings.gates', 'SortingGatesController');

});