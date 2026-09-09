<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentMethodController;
Route::middleware('admin')->group(function(){

Route::get('paymentmethods',
    [PaymentMethodController::class,'index']
)->name('common.payment_method');

Route::post('payment_method',
    [PaymentMethodController::class,'store']
)->name('payment_method.store');

Route::put('payment_method/{id}',
    [PaymentMethodController::class,'update']
)->name('payment_method.update');

Route::delete('paymentmethods/{id}',
    [PaymentMethodController::class,'destroy']
)->name('payment_method.destroy');
});