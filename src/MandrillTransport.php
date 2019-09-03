<?php

namespace LaravelMandrill;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\MailServiceProvider as MailProvider;
use LaravelMandrill\MandrillTransport;
use LaravelMandrill\MandrillTransportManager;
use Swift_Mailer;

class MandrillServiceProvider extends MailProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
        
        if ($this->app['config']['mail.driver'] == 'mandrill') {
            $this->app->singleton('swift.transport', function ($app) {
                return new MandrillTransportManager($app);
            });
        }
    }
}
