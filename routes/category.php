<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

/* CATEGORY CRUD */
Route::middleware('admin')->group(function(){

    Route::prefix('maxpos')->group(function(){
        Route::get('/category', [CategoryController::class, 'index'])
                    ->name('product_master.category');

        Route::post('/category/store', [CategoryController::class, 'store'])
            ->name('categories.store');

        Route::put('/update/{category}', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::delete('/category/delete/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        Route::get('/show/{category}', [CategoryController::class, 'show'])
            ->name('categories.show');
       });       

 });