<?php

use App\Http\Controllers\StockMovementController;

Route::middleware('admin')->group(function(){

Route::prefix('maxpos')->group(function() {
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
    Route::post('/stock-movements', [StockMovementController::class,'store'])->name('stock-movements.store');

    Route::delete('/stock-movements/{id}', [StockMovementController::class,'destroy'])->name('stock-movements.destroy');
    Route::delete('/stock-movements/bulk-delete', [StockMovementController::class,'bulkDelete'])->name('stock-movements.bulkDelete');

    Route::get('/stock-movements/{id}/edit', [StockMovementController::class,'edit'])->name('stock-movements.edit');
    Route::put('/stock-movements/{id}', [StockMovementController::class,'update'])->name('stock-movements.update');

    Route::get('/stock-movements/bulk-edit-json', [StockMovementController::class,'bulkEditJson'])->name('stock-movements.bulk-edit-json');
    Route::put('/stock-movements/bulk-update', [StockMovementController::class,'bulkUpdate'])->name('stock-movements.bulk-update');

    Route::get('/stock/variant/{id}', [StockMovementController::class,'variantStock']);
});

});
