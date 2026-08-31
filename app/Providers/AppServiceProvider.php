<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Foundation\FileBasedMaintenanceMode;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MaintenanceMode::class, function () {
            return new FileBasedMaintenanceMode();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Config::set('database.default', 'pgsql');

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            URL::forceScheme('https');
        }
    }
}
