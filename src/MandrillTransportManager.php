<?php

namespace LaravelMandrill;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Manager;
use LaravelMandrill\MandrillTransport;

class MandrillTransportManager extends Manager {
    
    /**
     * Create an instance of the Mandrill Swift Transport driver.
     *
     * @return \Illuminate\Mail\Transport\MandrillTransport
     */
    protected function createMandrillDriver()
    {
        $config = $this->app['config']->get('services.mandrill', []);
        return new MandrillTransport(
            $this->guzzle($config), $config['secret']
        );
    }
}