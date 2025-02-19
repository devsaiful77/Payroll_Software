<?php

use Illuminate\Support\Facades\Route;
use Modules\Account\Http\Controllers\{SalesProductInfoController};



Route::prefix('admin/accounting')->name('admin.accounting.')->middleware('admin', 'auth')->group(function () {

    Route::get('product/list', [SalesProductInfoController::class, 'index'])->name('product.list');

    /* ================ Sales controller ================ */ 
    Route::get('sale/list', 'SalesController@index')->name('sale.list');
    Route::get('sale/create', 'SalesController@create')->name('sale.create');


});




