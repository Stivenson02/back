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
        Route::post('file_avatar', 'UserController@uploadAvatar');
        Route::post('file_logo', 'UserController@uploadLogo');
        Route::post('file_identity', 'UserController@uploadIdentity');
        Route::post('file_nit', 'UserController@uploadNit');
        Route::post('file_rut', 'UserController@uploadRut');
        Route::post('file_chamber_commerce', 'UserController@uploadChamberCommerce');
        Route::put('update', 'UserController@update');
        Route::get('detail', 'UserController@index');

        Route::get('file_all', 'UserController@getAll');
        Route::get('file_avatar', 'UserController@getAvatar');

        Route::delete('file_delete/{document_id}', 'UserController@deleteDocument');
    });

    Route::prefix('general')->group(function () {
        Route::get('departaments', 'General\GeneralController@getDepartament');
        Route::get('cities/{id}', 'General\GeneralController@getCities');
        Route::get('categories', 'General\GeneralController@getCategories');
    });

    Route::prefix('product')->group(function () {

        Route::post('upload_image', 'Administration\ProductController@uploadProductDetail');
        Route::post('new_product', 'Administration\ProductController@storeNewProducts');
        Route::post('new/upload_image', 'Administration\ProductController@uploadNewProductDetail');

        Route::put('change_image', 'Administration\ProductController@changeImageDefault');
        Route::put('edit/{product_id}', 'Administration\ProductController@updateProduct');

        Route::get('all', 'Administration\ProductController@getProductAll');
        Route::get('detail/{product_id}', 'Administration\ProductController@getProductDetail');
        Route::get('new/all', 'Administration\ProductController@shoproducts_saleswAllNewProduct');
        Route::get('new/detail/{product_id}', 'Administration\ProductController@getDetailNewProduct');

        Route::delete('delete_image/{id_image}', 'Administration\ProductController@deleteImageProducts');
        Route::delete('new/delete_image/{id_image}', 'Administration\ProductController@deleteImageNewProducts');
        Route::delete('new/product/{id_pr}', 'Administration\ProductController@deleteNewProducts');
    });

    Route::prefix('report')->group(function () {
        Route::post('products_sales', 'Report\SupplierController@getSales');
        Route::post('products_client', 'Report\SupplierController@getClient');

        Route::get('products_order', 'Report\ServiceOrdersController@getSalesProduct');
        Route::get('order_sales', 'Report\ServiceOrdersController@getListDeparture');

    });

    Route::prefix('dash')->group(function () {

        Route::get('products_sales', 'Report\SupplierController@getDahsSales');

    });

});



