<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class IpApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('IpApiService', function()
        {
            return new \App\Services\IpApiService;
        });
    }
}
