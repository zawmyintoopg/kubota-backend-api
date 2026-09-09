<?php

use App\Http\Controllers\StockController;
Route::middleware('admin')->group(function(){

Route::get('/stock-dashboard', [StockController::class, 'stockBalance'])->name('stock.balance');
});