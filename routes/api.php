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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/products', 'ProductsController@list');
    Route::post('/products/{$id}', 'ProductsController@store');
    Route::put('/products/{$id}', 'ProductsController@update');
    Route::post('/products/image/{$id}', 'ProductsController@uploadImage');
    Route::delete('/products/{$id}', 'ProductsController@delete');
    Route::get('products/{$id}', 'ProductsController@restore');
    Route::delete('products/image/{$id}', 'ProductsController@deleteImage');
    Route::get('products/{$id}', 'ProductsController@show');
});
