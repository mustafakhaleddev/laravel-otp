<?php

namespace MKD\LaravelOTP;

use Illuminate\Support\ServiceProvider;

final class LaravelOTPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-advanced-otp.php'),
            ], 'config');


        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Register the main class to use with the facade
        $this->app->singleton('laravel-otp', function () {
             return new LaravelOTP;
        });
    }
}
