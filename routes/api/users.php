<?php

use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->controller(UserController::class)->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::get('/', 'index');

        Route::post('signin', 'login');
        Route::post('signup', 'register');
    });
});
