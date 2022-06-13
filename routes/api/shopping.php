<?php

use App\Http\Controllers\API\ShoppingController;
use Illuminate\Support\Facades\Route;

Route::prefix('shopping')->controller(ShoppingController::class)->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::get('/', 'index');
        Route::get('/{shoppingId}', 'detail');

        Route::post('/', 'create');

        Route::put('/{shoppingId}', 'update');

        Route::delete('/{shoppingId}', 'destroy');
    });
});
