<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitController;

/* unit CRUD */
Route::middleware('admin')->group(function(){

    Route::prefix('maxpos')->group(function(){
        Route::get('/units', [UnitController::class, 'index'])
                    ->name('product_master.unit');

        Route::post('units/store', [UnitController::class, 'store'])
            ->name('unit.store');

        Route::put('/units/update/{unit}', [UnitController::class, 'update'])
            ->name('unit.update');

        Route::delete('/units/delete/{unit}', [UnitController::class, 'destroy'])
            ->name('unit.destroy');

        Route::get('/show/{unit}', [UnitController::class, 'show'])
            ->name('unit.show');
       });       

 });