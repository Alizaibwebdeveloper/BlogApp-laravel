<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Redirect to the admin dashboard if the user is already authenticated
        RedirectIfAuthenticated::redirectUsing(function(){
            return route('admin.dashboard');
        });

        // Redirect to the login page if the user is not authenticated
        Authenticate::redirectUsing(function(){
            Session::flash('error', 'You need to login to access the admin dashboard');
            return route('admin.login'); // Assuming 'login' is the name of your login route
        });
    }
}