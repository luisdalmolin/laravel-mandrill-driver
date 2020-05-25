<?php

namespace LaravelMandrill;

use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving(\Illuminate\Mail\MailManager::class, function (\Illuminate\Mail\MailManager $mail_manager) {
            $mail_manager->extend("mandrill", function () {
                $config = $this->app['config']->get('services.mandrill', []);
                return new \LaravelMandrill\MandrillTransport(new \GuzzleHttp\Client($config), $config['secret']);
            });
        });
    }
}
