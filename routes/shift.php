<?php
use App\Http\Controllers\ShiftController;

Route::get('/shifts', [ShiftController::class, 'index'])
    ->name('shifts.index');

Route::post('/shifts/store', [ShiftController::class, 'store'])
    ->name('shifts.store');

Route::post('/shifts/{shift}/close', [ShiftController::class, 'closeShift'])
    ->name('shifts.close');

//Route::get('/shifts/{shift}/pdf', [ShiftController::class, 'downloadPdf'])->name('shift.pdf');