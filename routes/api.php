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


Route::get('/stock-adjustment-reasons', 'AdjustmentReasonController@index');
Route::post('/stock-adjustment-reasons', 'AdjustmentReasonController@store');

//pos

Route::get('/stock-adjustments', 'AdjustmentController@index');
Route::post('/stock-adjustments', 'AdjustmentController@store');

Route::get('/sales', 'SaleController@index');
Route::post('/sales', 'SaleController@store');
Route::get('/sales/{sale}/', 'SaleController@show');
Route::get('/sales/{sale}/cancel', 'SaleController@cancel');

Route::get('/purchases', 'PurchaseController@index');
Route::get('/purchases/{purchase}', 'PurchaseController@show');
Route::post('/purchases', 'PurchaseController@store');
Route::get('/purchases/{purchase}/cancel', 'PurchaseController@cancel');

Route::get('/settings/shop-setup', 'ConfigController@index');
Route::post('/settings/shop-setup', 'ConfigController@store');
Route::put('/settings/shop-setup', 'ConfigController@update');

Route::get('/seetings/backup', 'ConfigController@getBackup');
Route::get('/seetings/backup/create', 'ConfigController@createBackup');
Route::get('/seetings/restore', 'ConfigController@restoreBackup');

Route::get('/customers', 'CustomerController@index');
Route::post('/customers', 'CustomerController@store');
Route::put('/customers{customer}', 'CustomerController@update');
Route::delete('/customers/{customer}', 'CustomerController@destroy');


Route::get('/suppliers', 'SupplierController@index');
Route::post('/suppliers', 'SupplierController@store');
Route::put('/suppliers/{supplier}', 'SupplierController@update');
Route::delete('/suppliers/{supplier}', 'SupplierController@destroy');


Route::get('/admin/users', 'AdminController@index');
Route::get('/admin/users/{user}', 'AdminController@show');
Route::put('/admin/users/{user}/assign-role', 'AdminController@assignRole');
Route::delete('/admin/users/{user}', 'AdminController@destroy');
Route::get('/admin/create-backup', 'AdminController@createBackup');
Route::get('/admin/restore-backup/{backup}', 'AdminController@restoreBackup');

Route::post('/out-payments', 'OutPaymentController@store');
Route::post('/in-payments', 'InPaymentController@store');