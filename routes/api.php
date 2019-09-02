<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;

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

Route::get('/User/roles', [
    'uses' => 'UsersController@UserRoles',
    'as' => 'user.roles'
]);

Route::prefix('v1')->group(function () {
    Route::post('/login', 'ApiAuthController@login');
    Route::post('/register', 'ApiyAuthController@register')->name('register');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/getUser', 'Api\AuthController@getUser');
    });
});

Route::post('/forgot/password', 'ForgotpasswordController')->name('forgot.password');
//Route::middleware('auth:api')->group(function () {
Route::get('/products', 'ProductsController@list');
Route::get('productsc/{$id}', 'ProductsController@show');

Route::middleware('Cors')->group(function () {
    Route::get('/categories', 'CategoriesController@index');
    Route::post('/categories/create', 'CategoriesController@store');
    Route::put('/categories/{$id}', 'CategoriesController@update');
    Route::post('/categories/image/{$id}', 'ProductsController@uploadImage');
    Route::delete('/categories/{$id}', 'CategoriesController@delete');
    Route::get('categories{$id}', 'CategoriesController@restore');
    Route::delete('/categories/image/{$id}', 'CategoriesController@deleteImage');
    Route::get('/categories/{$id}', 'CategoriesController@show');
});


Route::get('/cart', 'CartsController@list');
Route::post('/cart/{$id}', 'CartsController@addItem');
Route::post('/cart/{$id}', 'CartsController@deleteItem');
Route::put('/cart/{$id}', 'CartsController@UpdateCart');
Route::get('/cart/{$id}', 'CartsController@restoreCart');

Route::get('/orders', 'OrdersController@orders');


Route::get('/checkout', 'CheckoutController@index')->name('checkout.index');
//});

Route::middleware('isAdmin')->group(function () {
    Route::post('roles/create', 'RolesController@createRole')->name('role.create');
    Route::get('role/{id}', 'RolesController@get');
    Route::put('role/update{id}', 'RolesController@update')->name('role.update');
    Route::delete('role/delete{id}', 'RolesController@delete')->name('role.delete');
    Route::delete('role/delete/user{id}', 'RolesController@deleteUser')->name('role.user.delete');
    Route::post('/products/{$id}', 'ProductsController@store');
    Route::put('/products/{$id}', 'ProductsController@update');
    Route::post('/products/image/{$id}', 'ProductsController@uploadImage');
    Route::delete('/products/{$id}', 'ProductsController@delete');
    Route::get('products/{$id}', 'ProductsController@restore');
    Route::delete('products/image/{$id}', 'ProductsController@deleteImage');
});


// Route::view('/checkout', 'checkout');
// Route::view('/thankyou', 'thankyou');

Route::middleware('auth::api')->prefix('admin');

Route::get('/admin', 'AdminController@admin')
    ->middleware('isAdmin')
    ->name('admin');
