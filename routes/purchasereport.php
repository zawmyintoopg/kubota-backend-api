<?php
use App\Http\Controllers\PurchaseReportController;

Route::middleware('admin')->group(function(){

Route::prefix('maxpos')->group(function(){
    Route::get('/purchase-report', [PurchaseReportController::class, 'index'])->name('purchase.report'); // main page
    Route::get('/detail', [PurchaseReportController::class, 'detail'])->name('purchase.detail'); // table view
    Route::get('/top-products', [PurchaseReportController::class, 'topProducts'])->name('purchase.top-products'); // top product

    // Export
    Route::get('/export/pdf', [PurchaseReportController::class, 'purchasePdf'])->name('purchase.pdf');
    Route::get('/export/excel', [PurchaseReportController::class, 'purchaseExcel'])->name('purchase.excel');
   });
});