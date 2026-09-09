<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
Route::middleware('admin')->group(function(){

Route::get('/maxpos/register', [RegisteredUserController::class, 'create'])
        ->name('customize_register');

Route::post('customize_register', [RegisteredUserController::class, 'store'])
       ->name('customize_store');
Route::get('home', [RegisteredUserController::class, 'dashboard'])
        ->name('home');
Route::get('maxpos/login', [AuthenticatedSessionController::class, 'create'])
        ->name('maxlogin');
//
Route::middleware('admin')->group(function(){

   Route::prefix('maxpos')->group(function(){
        Route::get('admin',function(){
                        return view('customize.admin');
                })->name('adminPage');
                Route::get('admin',function(){
                        return view('customize.admin');
                })->name('adminPage');
       });       

 });

Route::middleware('cashier')->group(function(){
   Route::prefix('maxpos')->group(function(){
        Route::get('cashier',function(){
                return view('customize.cashier');
                })->name('cashierPage');
        });
   });

Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
->name('password.email');

Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
->name('password.reset');

Route::post('reset-password', [NewPasswordController::class, 'store'])
->name('password.store');
});