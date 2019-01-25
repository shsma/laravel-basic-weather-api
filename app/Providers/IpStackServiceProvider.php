<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class IpStackServiceProvider extends ServiceProvider
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
        $this->app->bind('IpStackService', function()
        {
            return new \App\Services\IpStackService;
        });
    }
}
