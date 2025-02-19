<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('/api/admin/accounting')->name('admin.accounting.')->middleware('admin', 'auth')->group(function () {

    /* ================ Sales controller ================ */ 
    Route::get('sale/list', 'SalesController@index');


    /* ================ Sales controller ================ */ 
    // Route::get('sale/list', 'SalesController@index')
    // Route::get('sale/create', 'SalesController@create');


});