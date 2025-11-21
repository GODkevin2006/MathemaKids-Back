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
        // Aquí registras servicios, bindings, singletons, etc.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Aquí colocas configuraciones que se ejecutan al iniciar la app
    }
}
