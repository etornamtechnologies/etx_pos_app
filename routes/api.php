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

Route::get('/user/info', 'UserController@getAuthUserInfo');

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
Route::post('/products/create-csv', 'ProductController@storeProductsWithCsv');


Route::get('/inventory/products/{product}/stock-units/{stock_unit}/add', 'InventoryController@addStockUnit');
Route::get('/inventory/products/{product}/stock-units/{stock_unit}/remove', 'InventoryController@removeStockUnit');
Route::put('/inventory/products/{product}/stock-units/{stock_unit}/update-cost-price', 'InventoryController@updateCostPrice');
Route::put('/inventory/products/{product}/stock-units/{stock_unit}/update-selling-price', 'InventoryController@updateSellingPrice');
Route::put('/inventory/products/{productId}/update-stock-quantity', 'InventoryController@updateStockQuantity');

Route::post('/inventory/price-templates/{templates}/apply', 'InventoryController@applyPriceTemplate');

Route::apiResource('/price-templates', 'PriceTemplateController');


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
Route::put('/customers/{customer}', 'CustomerController@update');
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
Route::post('/admin/users/{user}/reset-password', 'AdminController@resetPassword');

Route::apiResource('/users', 'UserController');

Route::post('/out-payments', 'OutPaymentController@store');
Route::post('/in-payments', 'InPaymentController@store');

Route::get('/dashboard', 'DashboardController@index');

Route::post('reports/sale/transaction', 'ReportController@getSaleReportByTransaction');
Route::post('reports/sale/product', 'ReportController@getSaleReportByProduct');

Route::post('reports/purchase/transaction', 'ReportController@getPurchaseReportByTransaction');
Route::post('reports/purchase/product', 'ReportController@getPurchaseReportByProduct');

Route::post('reports/financial', 'ReportController@getFinancialReport');


Route::post('reports/stock-adjustment/transaction', 'ReportController@getStockAdjustmentReportByTransaction');
Route::post('reports/stock-adjustment/product', 'ReportController@getStockAdjustmentReportByProduct');


Route::get('batches/expiry-alert', 'BatchController@expiryAlertList');

Route::get('alerts/restock-list', 'NotificationController@getProductRestockList');
Route::get('alerts/expiry-list', 'NotificationController@getProductExpiryList');

Route::get('ledgers/sales-with-debt', 'LedgerController@salesWithDebt');
Route::get('ledgers/purchases-with-credit', 'LedgerController@purchasesWithCredit');
Route::get('ledgers/batches', 'LedgerController@getAllProductBatches');

Route::put('batches/{batch}', 'BatchController@update');
Route::delete('batches/{batch}', 'BatchController@destroy');
Route::post('batches', 'BatchController@addBatchToProduct');