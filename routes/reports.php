<?php
use App\Http\Controllers\ReportController;

Route::middleware(['auth'])->group(function () {

    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/sales/pdf', [ReportController::class, 'salesPdf'])->name('reports.sales.pdf');
    Route::get('/reports/sales/excel', [ReportController::class, 'salesExcel'])->name('reports.sales.excel');
    Route::get('reports/sales/pdf', [ReportController::class,'exportPdf'])->name('reports.sales.pdf');
    Route::get('reports/profit-loss', [App\Http\Controllers\ReportController::class, 'profitLossForm'])->name('reports.profit_loss.form');
    Route::get('reports/profit-loss/data', [App\Http\Controllers\ReportController::class, 'profitLossData'])->name('reports.profit_loss.data');

}); 
