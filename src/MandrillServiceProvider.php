<?php

namespace LaravelMandrill;

use Illuminate\Support\Facades\Mail;
use MailchimpTransactional\ApiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Mail::extend('mandrill', function () {
            $client = new ApiClient();
            $client->setApiKey(Config::get('services.mandrill.secret'));

            $headers = Config::get('services.mandrill.headers', []);

            return new MandrillTransport($client, $headers);
        });
    }
}
