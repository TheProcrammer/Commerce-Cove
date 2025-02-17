<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\CartService;
use Illuminate\Support\Facades\Schedule;

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

   
    // Used to configure application-level services when the application starts.
    // Command to run automatically every month on the 1st day at midnight (00:00):
    public function boot(): void
    {
        Schedule::command('payout:vendors')
            ->monthlyOn(1,'00:00') // Runs on the 1st day of the month at 12:00 AM
            ->withoutOverlapping();
        Vite::prefetch(concurrency: 3);
    }
}
