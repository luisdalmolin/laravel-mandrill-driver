<?php
namespace IGD\Mandrill\Providers;

use IGD\Mandrill\Transport;
use Illuminate\Mail\MailManager;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Register the mandrill mail transport
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('mandrill', function () {
                return new Transport();
            });
        });

        // Register the classes to use with the facade
        $this->app->bind('mandrill', 'IGD\Mandrill\Mandrill');
    }
}
