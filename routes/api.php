<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')
    ->group(function () {

        Route::prefix('auth')->name('auth.')->controller(AuthController::class)
            ->group(function () {
                Route::post('register', 'register')->name('register');
                Route::post('login', 'login')->name('login');
                Route::post('logout', 'logout')->name('logout');
                Route::get('me', 'me')->name('me');
            });

        Route::middleware('auth:api')->group(function () {
            Route::resource('tasks', TaskController::class)->except(['create', 'edit']);
        });
    });
