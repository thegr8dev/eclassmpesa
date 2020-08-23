<?php

namespace Thegr8dev\Eclassmpesa;

use Illuminate\Support\ServiceProvider;

class MPesaAddonProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/views','eclassmpesa');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations/');

        $this->publishes([
            __DIR__.'/views/other' => resource_path('views/vendor/eclassmpesa'),
            __DIR__.'/views/admin/sidebar' => resource_path('views/admin/layouts'),
            __DIR__.'/views/front' => resource_path('views/front'),
            __DIR__.'/database/migrations/' => database_path('migrations'),
            __DIR__.'/images' => public_path('images/payment'),
            __DIR__.'/assets/js' => public_path('js'),
            __DIR__.'/config/mpesa.php' => config_path('mpesa.php')
        ]);
    }
}
