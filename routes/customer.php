<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
Route::middleware('admin')->group(function(){

/* customer CRUD */
Route::get('/customers', [CustomerController::class, 'index'])
    ->name('sale_master.customer');

Route::post('/customers', [CustomerController::class, 'store'])
    ->name('customer.store');

Route::put('/customers/{customer}', [CustomerController::class, 'update'])
    ->name('customer.update');

Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])
    ->name('customer.destroy');

/* STATUS TOGGLE (OPTIONAL) */
Route::put('/customers/{customer}/status', [CustomerController::class, 'updateStatus'])
    ->name('customer.status');
});