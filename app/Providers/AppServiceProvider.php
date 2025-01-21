<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
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
    public function boot()
    {
        // Check if the app is running in console or is in development mode
        if (!$this->app->runningInConsole()) {
            $this->startServer();
        }
    }

    protected function startServer()
    {
        // Run the artisan command to start the server
        Artisan::call('nativephp:start-server');
    }
}
