<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        if ($this->app->runningInConsole() || ! $this->app->environment('production')) {
            return;
        }

        $migrationFiles = glob(database_path('migrations/*.php')) ?: [];
        $signature = sha1(implode('|', array_map(
            static fn (string $file): string => basename($file).':'.filemtime($file),
            $migrationFiles
        )));
        $marker = storage_path('framework/.cnet-deployed-'.$signature);

        if (is_file($marker)) {
            return;
        }

        try {
            @set_time_limit(120);
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--force' => true]);
            @file_put_contents($marker, now()->toIso8601String());
        } catch (Throwable $exception) {
            Log::error('Automatic deployment setup failed.', [
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
