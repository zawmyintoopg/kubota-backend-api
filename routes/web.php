<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LangController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
Route::get('/maxpos', function () {
    return view('main.login');
})->name('login');

require __DIR__.'/login.php';
require __DIR__.'/category.php';
require __DIR__.'/brand.php';
require __DIR__.'/product.php';
require __DIR__.'/supplier.php';
require __DIR__.'/transaction_type.php';
require __DIR__.'/payment_method.php';
require __DIR__.'/purchase.php';
require __DIR__.'/sales.php';
require __DIR__.'/varient.php';
require __DIR__.'/unit.php';
require __DIR__.'/stockmovement.php';
require __DIR__.'/purchasereport.php';
require __DIR__.'/stockbalance.php';
require __DIR__.'/shift.php';
require __DIR__.'/reports.php';





