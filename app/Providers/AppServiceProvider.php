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
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || env('VERCEL') || env('APP_STORAGE')) {
            URL::forceScheme('https');

            $dbConnection = env('DB_CONNECTION', 'pgsql');
            Config::set('database.default', $dbConnection);

            if ($dbConnection === 'sqlite') {
                $sqlitePath = file_exists('/tmp/database.sqlite') ? '/tmp/database.sqlite' : database_path('database.sqlite');
                Config::set('database.connections.sqlite.database', $sqlitePath);
            }
        }
    }
}
