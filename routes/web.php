<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

/*
test routes
*/

Route::view('/example-page','example-page');
Route::view('/example-auth','example-auth');



/* 
Admin Routes
*/

Route::prefix('admin')->name('admin.')->group(function(){
    Route::middleware([])->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::get('/login','loginForm')->name('login');
            Route::get('/forgot-password','forgotPasswordForm')->name('forgot-password');
            Route::post('/login', 'loginHandler')->name('login-handler');
        });
    });

    Route::middleware(['auth'])->group(function(){
        Route::controller(AdminController::class)->group(function(){
            Route::get('/dashboard','dashboard')->name('dashboard');
            
        });
    });
});
