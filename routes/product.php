
<?php

use App\Models\Product;
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProductController;

Route::middleware('admin')->group(function(){

    Route::prefix('maxpos')->group(function(){
/* CATEGORY CRUD */
    Route::get('/productcreate', [ProductController::class, 'productcreate'])
        ->name('productcreate');

        Route::post('/product/store', [ProductController::class, 'store'])
            ->name('product.store');

        Route::put('/product/{product}', [ProductController::class, 'update'])
            ->name('product.update');

        Route::delete('/product/{product}', [ProductController::class, 'destroy'])
            ->name('product.destroy');

        /* EXTRA */
        Route::get('/product/{product}', [ProductController::class, 'show'])
            ->name('product.show');

        Route::put('/product/{product}/status', [ProductController::class, 'updateStatus'])
            ->name('product.status');


        Route::put('/product/variant/index', [ProductController::class, 'varient_index'])
            ->name('product.variant.index');

        Route::get('/price-history', [ProductController::class, 'priceHistoryIndex'])
            ->name('price.history.index');

    Route::get('/quick.category', [ProductController::class, 'quick_category'])
            ->name('quick.category');
    
    Route::get('/quick.brands', [ProductController::class, 'quick_brand'])
    ->name('quick.brand');

    Route::get('/quick.unit', [ProductController::class, 'quick_unit'])
    ->name('quick.unit');      
    });
   
    Route::get('/item.create', [ItemController::class, 'create'])
    ->name('items.create');

    Route::get('/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
  
    Route::get('/item.search', [ItemController::class, 'search'])
    ->name('items.search');    

    Route::get('/item.list', [ItemController::class, 'index'])
    ->name('items.list');

    Route::get('/item.prodcut', [ItemController::class, 'index'])
    ->name('product_master.productList');
    
    Route::post('/items/{product}/deactivate', [ItemController::class, 'deactivate'])
    ->name('items.deactivate');

    Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

    Route::post('/items/{product}/activate', [ItemController::class, 'activate'])
        ->name('items.activate');    

    Route::get('/items/last-code', [ItemController::class, 'lastCode'])->name('items.lastCode');
    Route::post('/items/store', [ItemController::class, 'store'])->name('items.store');

});
    use App\Http\Controllers\PriceHistoryController;

/* ================= PRICE HISTORY ================= */
Route::prefix('price-history')
    ->name('price.history.')
    ->middleware(['auth']) // keep auth
    ->group(function () {

        // LIST PAGE
        Route::get('/', [PriceHistoryController::class, 'index'])
            ->name('index');

        // ROLLBACK PRICE (ADMIN ONLY)
        Route::post('/rollback/{id}', [PriceHistoryController::class, 'rollback'])
            ->middleware('can:isAdmin') // or custom admin middleware
            ->name('rollback');
    });
