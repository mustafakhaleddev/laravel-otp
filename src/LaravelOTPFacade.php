<?php

namespace MKD\LaravelOTP;

use Illuminate\Support\Facades\Facade;

class LaravelOTPFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-otp';
    }
}
