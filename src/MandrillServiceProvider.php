<?php

namespace LaravelMandrill;

use Illuminate\Mail\MailServiceProvider;
use LaravelMandrill\MandrillTransport;

class MandrillServiceProvider extends MailServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMandrillSwiftMailer();
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] == 'mandrill') {
            $this->registerMandrillSwiftMailer();
        } else {
            parent::registerSwiftMailer();
        }
    }

    /**
     * Register the Mandrill swift mailer
     */
    public function registerMandrillSwiftMailer()
    {
        $this->app['swift.mailer'] = $this->app->share(function ($app) {
            return new \Swift_Mailer(new MandrillTransport());
        });
    }
}
