<?php
use App\Http\Controllers\SaleController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| sale Routes
|--------------------------------------------------------------------------
*/
Route::middleware('cashier')->group(function(){

    Route::prefix('maxpos')->group(function(){
/* 📄 LIST PAGE */
Route::get('/sales', [SaleController::class, 'index'])
    ->name('salePage');

/* ➕ CREATE PAGE */
Route::get('/sales/create', [SaleController::class, 'create'])
    ->name('saleCreate');

/* ✏️ EDIT PAGE */

/* 🔄 UPDATE */
/* 🗑 DELETE */
Route::delete('/sale/{id}', [SaleController::class, 'destroy'])
    ->name('sales_master.destroy');

/* 🖨 PRINT VIEW */
Route::get('/sale/print/{id}', [SaleController::class, 'print'])
    ->name('sales.print');

/* 📄 PDF EXPORT */
Route::get('/sale/pdf/{id}', [SaleController::class, 'pdf'])
    ->name('sales_master.pdf');

Route::get('/product-search', [SaleController::class, 'productSearch'])
    ->name('products.search');
    });
Route::get('/sales/print/{id}', [SaleController::class, 'print'])
    ->name('sales_master.print');

Route::post('/sales/salesave', [SaleController::class, 'store'])->name('sales.save');

Route::post('/customer/store', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/sales/{id}/edit', [SaleController::class, 'edit'])->name('sales_edit');
Route::post('/sales/update/{id}', [SaleController::class, 'update'])->name('sales.update');
// web.php
Route::delete('/sales/item/{saleDetail}', [SaleController::class, 'deleteItem'])->name('delete.item');


Route::get('/sales/{id}/print', [SaleController::class, 'print'])->name('sales.print');

});