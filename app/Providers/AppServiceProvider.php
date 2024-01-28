<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //Directiva para mostrar cosas solo si se es superadmin
        Blade::if('admin', function () {
            if (isAdmin()===true) {
                return true;
            } 
            return false;
        });

        Blade::if('local', function () {
            if (config('app.env') == 'local') {
                return true;
            } 
            return false;
        });
    }
    
}
