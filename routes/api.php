<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix('user')->group(function () {
        Route::get('detail', 'UserController@index');
        Route::put('update', 'UserController@update');
        Route::post('file_image', 'UserController@documentImages');
        Route::post('file_document', 'UserController@documentFiles');
    });
    Route::prefix('general')->group(function () {
        Route::get('departaments', 'General\GeneralController@getDepartament');
        Route::get('cities/{id}', 'General\GeneralController@getCities');
    });
});



