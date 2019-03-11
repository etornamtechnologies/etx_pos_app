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


Route::post('/auth/login', 'AuthController@login');
Route::post('/auth/logout', 'AuthController@logout');
Route::post('/auth/register', 'AuthController@register');


Route::get('/categories', 'CategoryController@index');
Route::post('/categories', 'CategoryController@store');
Route::put('/categories/{category}', 'CategoryController@update');
Route::delete('/categories/{category}', 'CategoryController@destroy');

Route::get('/manufacturers', 'ManufacturerController@index');
Route::post('/manufacturers', 'ManufacturerController@store');
Route::put('/manufacturers', 'ManufacturerController@update');
Route::delete('/manufacturers', 'ManufacturerController@destroy');

Route::get('/stock-units', 'StockUnitController@index');
Route::post('/stock-units', 'StockUnitController@store');
Route::put('/stock-units/{id}', 'StockUnitController@update');
Route::delete('/stock-units/{id}', 'StockUnitController@destroy');

Route::get('/products', 'ProductController@index');
Route::get('/products/{product}', 'ProductController@show');
Route::post('/products', 'ProductController@store');
Route::put('/products/{product}', 'ProductController@update');
Route::delete('/products/{product}', 'ProductController@destroy');
Route::post('/products/entries', 'ProductController@storeEntries');

Route::get('/inventory/products/{product}/stock-units/{stock_unit}/add', 'InventoryController@addStockUnit');
Route::get('/inventory/products/{product}/stock-units/{stock_unit}/remove', 'InventoryController@removeStockUnit');