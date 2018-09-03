<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(array('prefix' => 'api/v1', 'middleware' => 'verify_access_token'), function () {
    Route::group(['namespace' => 'Api'], function () {
        Route::get('sorting_gates', ['uses' => 'SortingGatesController@search']);
    });
});
