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
        // Auto-provision SQLite database on cloud deployments (Railway, etc.)
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && $dbPath !== ':memory:') {
                if (!file_exists($dbPath) || filesize($dbPath) === 0) {
                    @touch($dbPath);
                    try {
                        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error('Auto migration failed: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
