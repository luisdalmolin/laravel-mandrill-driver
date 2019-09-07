<?php

namespace LaravelMandrill;

use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['swift.transport']->extend('mandrill', function () {
            $config = $this->app['config']->get('services.mandrill', []);

            return new MandrillTransport(new \GuzzleHttp\Client($config), $config['secret']);
        });
    }
}
