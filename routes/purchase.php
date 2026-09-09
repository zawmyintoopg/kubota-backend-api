<?php
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| sale Routes
|--------------------------------------------------------------------------
*/
Route::middleware('admin')->group(function(){

    Route::prefix('maxpos')->group(function(){
/* 📄 LIST PAGE */
Route::get('/purchases', [PurchaseController::class, 'index'])
    ->name('purchasePage');

/* ➕ CREATE PAGE */
Route::get('/purchases/create', [PurchaseController::class, 'create'])
    ->name('purchaseCreate');

/* ✏️ EDIT PAGE */

/* 🔄 UPDATE */
/* 🗑 DELETE */
Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])
    ->name('purchase_master.destroy');

/* 🖨 PRINT VIEW */
Route::get('/purchase/print/{id}', [PurchaseController::class, 'print'])
    ->name('purchase.print');

/* 📄 PDF EXPORT */
Route::get('/purchase/pdf/{id}', [PurchaseController::class, 'pdf'])
    ->name('purchase_master.pdf');

Route::get('/product-search', [PurchaseController::class, 'productSearch'])
    ->name('products.search');
    });
Route::get('/purchase/print/{id}', [PurchaseController::class, 'print'])
    ->name('purchase_master.print');

Route::post('/purchase/purchasesave', [PurchaseController::class, 'store'])->name('purchase.save');

Route::post('/supplier/store', [CustomerController::class, 'store'])->name('suppliers.store');
Route::get('/purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases_edit');
Route::post('/purchases/update/{id}', [PurchaseController::class, 'update'])->name('purchases.update');
// web.php
Route::delete('/purchases/item/{saleDetail}', [PurchaseController::class, 'deleteItem'])->name('delete.item');


Route::get('/purchases/{id}/print', [PurchaseController::class, 'print'])->name('purchases.print');

});