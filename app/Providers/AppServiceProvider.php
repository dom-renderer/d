<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Laravel\Passport\Passport::enableImplicitGrant();

        if (!defined('DATE_FORMAT')) {
            define("DATE_FORMAT","d-m-Y");
        }

        if (!defined('DATE_FORMAT_DATEPICKER_DISPLAY')) {
            define("DATE_FORMAT_DATEPICKER_DISPLAY","dd-mm-yyyy");
        }

        if (!defined('DATE_FORMAT_DATEPICKER_FORMAT')) {
            define("DATE_FORMAT_DATEPICKER_FORMAT","dd-mm-yy");
        }
    }
}
