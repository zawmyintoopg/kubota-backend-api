<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
Route::middleware('admin')->group(function(){

/* SUPPLIER CRUD */
Route::get('/suppliers', [SupplierController::class, 'index'])
    ->name('purchase_master.supplier');

Route::post('/suppliers', [SupplierController::class, 'store'])
    ->name('supplier.store');

Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])
    ->name('supplier.update');

Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])
    ->name('supplier.destroy');

/* STATUS TOGGLE (OPTIONAL) */
Route::put('/suppliers/{supplier}/status', [SupplierController::class, 'updateStatus'])
    ->name('supplier.status');
});