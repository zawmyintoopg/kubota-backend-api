<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;

/* brand CRUD */
Route::middleware('admin')->group(function(){

    Route::prefix('maxpos')->group(function(){
        Route::get('/brands', [BrandController::class, 'index'])
                    ->name('product_master.brand');

        Route::post('/brand/store', [BrandController::class, 'store'])
            ->name('brand.store');

        Route::put('/brand/update/{brand}', [BrandController::class, 'update'])
            ->name('brand.update');

        Route::delete('/delete/{brand}', [BrandController::class, 'destroy'])
            ->name('brand.destroy');

        Route::get('/show/{brand}', [BrandController::class, 'show'])
            ->name('brand.show');
       });       

 });