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

    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Detect if running as a NativePHP Desktop App
        // if (config('nativephp-internal.running')) {
        //     $this->configureDesktopPaths();
        // }
    }

    /**
     * Reconfigure SQLite and Storage paths for Windows AppData.
     */
    private function configureDesktopPaths(): void
    {
        $appData = getenv('APPDATA').'/Luponv2';

        // 1. Ensure directories exist
        if (! is_dir($appData)) {
            \Illuminate\Support\Facades\File::makeDirectory($appData, 0755, true);
            \Illuminate\Support\Facades\File::makeDirectory($appData.'/storage', 0755, true);
        }

        $targetDb = $appData.'/database.sqlite';
        $nativeDb = $appData.'/nativephp.sqlite';

        // 2. Handle First-Run Database Setup
        if (! file_exists($targetDb)) {
            $sourceDb = database_path('database.sqlite');

            if (file_exists($sourceDb)) {
                // Copy the seeded database from the installation folder
                copy($sourceDb, $targetDb);
            } else {
                // Fresh start: Initialize and seed
                config(['database.connections.sqlite.database' => $targetDb]);
                \Illuminate\Support\Facades\Artisan::call('migrate --force');
                \Illuminate\Support\Facades\Artisan::call('db:seed --force');
            }
        }

        // 3. Remap Application Config to AppData
        config(['database.connections.sqlite.database' => $targetDb]);
        config(['database.connections.nativephp.database' => $nativeDb]);
        config(['filesystems.disks.local.root' => $appData.'/storage']);

        // Ensure the SQLite connections are refreshed with the new paths
        \Illuminate\Support\Facades\DB::purge('sqlite');
        \Illuminate\Support\Facades\DB::purge('nativephp');
    }
}
