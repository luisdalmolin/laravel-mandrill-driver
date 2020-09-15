<?php

namespace LaravelMandrill;

use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('mandrill', function () {
                $config = $this->app['config']->get('services.mandrill', []);
                return new \LaravelMandrill\MandrillTransport(
                    new \GuzzleHttp\Client($config), $config['secret']
                );
            });
        });
    }
}
