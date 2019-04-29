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
        Route::post('file_avatar', 'UserController@uploadAvatar');
        Route::post('file_logo', 'UserController@uploadLogo');
        Route::post('file_identity', 'UserController@uploadIdentity');
        Route::post('file_nit', 'UserController@uploadNit');
        Route::post('file_rut', 'UserController@uploadRut');
        Route::post('file_chamber_commerce', 'UserController@uploadChamberCommerce');

        Route::delete('file_delete/{document_id}', 'UserController@deleteDocument');
        Route::get('file_all', 'UserController@getAll');
        Route::get('file_avatar', 'UserController@getAvatar');



    });
    Route::prefix('general')->group(function () {
        Route::get('departaments', 'General\GeneralController@getDepartament');
        Route::get('cities/{id}', 'General\GeneralController@getCities');
    });
});



