<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TransactionTypeController;

Route::middleware('admin')->group(function(){

/* SUPPLIER CRUD */
Route::get('transaction_types', 
    [TransactionTypeController::class,'index']
)->name('common.transaction_type');

Route::post('transaction_types', 
    [TransactionTypeController::class,'store']
)->name('transaction_types.store');

Route::put('transaction-types/{id}', 
    [TransactionTypeController::class,'update']
)->name('transaction_types.update');

Route::delete('transaction_types/{id}', 
    [TransactionTypeController::class,'destroy']
)->name('transaction_types.destroy');
});