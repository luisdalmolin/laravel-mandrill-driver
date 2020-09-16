<?php

namespace LaravelMandrill;

use GuzzleHttp\Client;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('mandrill', function () {
                $config = $this->app['config']->get('services.mandrill', []);
                return new MandrillTransport(
                    new Client($config), $config['secret']
                );
            });
        });
    }
}
