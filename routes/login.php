<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
    // go login

// Route::get('/maxpos', [AuthenticatedSessionController::class, 'create'])
//     ->name('maxpos_login');

    //check login
Route::post('maxpos/loginstore', [AuthenticatedSessionController::class, 'store'])
     ->name('login_store');
//check and go to related user role
Route::middleware('cashier')->group(function(){
    Route::prefix('maxpos')->group(function(){
            Route::get('/cashier',function(){
                    return view('main.cashier');
                    })->name('salePage');
        });
    });
//check and go to related user role
Route::middleware('admin')->group(function(){
    Route::prefix('maxpos')->group(function(){
        Route::get('/admin', [AdminDashboardController::class, 'index'])
                    ->name('adminPage');
        });
     });
//check and go to related user role
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/showprofile', [ProfileController::class, 'show'])->name('profile.show');
});
Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('maxpos_login'); // redirect to login page
})->name('logout');

