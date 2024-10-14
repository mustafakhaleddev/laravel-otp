<?php

namespace MKD\LaravelOTP;

use Illuminate\Support\ServiceProvider;

class LaravelOTPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
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
    public function register()
    {
        // Register the main class to use with the facade
        $this->app->singleton('laravel-otp', function () {
             return new LaravelOTP;
        });
    }
}
