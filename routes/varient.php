<?php

use App\Http\Controllers\ProductVariantController;
Route::middleware('admin')->group(function(){
Route::prefix('maxpos')->group(function () {

    Route::get('/product/{product}/variant',
        [ProductVariantController::class, 'index']
    )->name('product.variant.index');

    Route::post('{product}/variant',
        [ProductVariantController::class, 'store']
    )->name('product.variant.store');

    Route::put('variant/{variant}',
        [ProductVariantController::class, 'update']
    )->name('product.variant.update');

    Route::delete('variant/{variant}',
        [ProductVariantController::class, 'destroy']
    )->name('product.variant.destroy');

   });
});
