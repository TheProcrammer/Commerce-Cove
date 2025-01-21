<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\CartService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register a singleton instance of the CartService class in the service container.
        // Useful is for it to be accessible from anywhere in the application.
        $this->app->singleton(CartService::class,function()
        {
            // This closure creates and returns a single, shared instance of CartService
            // that will be used throughout the application.
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
