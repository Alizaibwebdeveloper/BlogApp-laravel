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
    Route::middleware(['guest'])->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::get('/login','loginForm')->name('login');
            Route::get('/forgot-password','forgotPasswordForm')->name('forgot-password');
            Route::post('/login', 'loginHandler')->name('login-handler');
            Route::post('/set-password-reset-link', 'setPasswordResetLink')->name('set-password-reset-link');
            Route::get('/reset-password/{token}', 'resetPasswordForm')->name('reset-password');
            Route::post('/reset-password-handler', 'resetPasswordHandler')->name('reset_password_handler');
        });
    });



    Route::middleware(['auth'])->group(function(){
        Route::controller(AdminController::class)->group(function(){
            Route::get('/dashboard','dashboard')->name('dashboard');
            
        });
    });
});
