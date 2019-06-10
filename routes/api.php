<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('v1')->group(function(){
    Route::post('/login', 'Api\AuthController@login');
    Route::post('/register', 'Api\AuthController@register');
    Route::group(['middleware' => 'auth:api'], function(){
        Route::post('/getUser', 'Api\AuthController@getUser');
    });
});

Route::post('/forgot/password', 'ForgotpasswordController')->name('forgot.password');

Route::get('/products', 'ProductsController@list');
Route::post('/products/{$id}', 'ProductsController@store');
Route::put('/products/{$id}', 'ProductsController@update');
Route::post('/products/image/{$id}', 'ProductsController@uploadImage');
Route::delete('/products/{$id}', 'ProductsController@delete');
Route::get('products/{$id}', 'ProductsController@restore');
Route::delete('products/image/{$id}', 'ProductsController@deleteImage');
Route::get('productsc/{$id}', 'ProductsController@show');

Route::get('/', 'CategoriesController@index');
Route::post('/categories/{$id}', 'CategoriesController@store');
Route::put('/categories/{$id}', 'CategoriesController@update');
Route::post('/categories/image/{$id}', 'ProductsController@uploadImage');
Route::delete('/categories/{$id}', 'CategoriesController@delete');
Route::get('categories{$id}', 'CategoriesController@restore');
Route::delete('/categories/image/{$id}', 'CategoriesController@deleteImage');
Route::get('/categories/{$id}', 'CategoriesController@show');

Route::get('/cart', 'CartsController@list');
Route::post('/cart/{$id}', 'CartsController@addItem');
Route::post('/cart/{$id}', 'CartsController@deleteItem');
Route::put('/cart/{$id}', 'CartsController@UpdateCart');
Route::get('/cart/{$id}', 'CartsController@restoreCart');


Route::get('/checkout', 'CheckoutController@index')->name('checkout.index');

// Route::view('/checkout', 'checkout');
// Route::view('/thankyou', 'thankyou');
