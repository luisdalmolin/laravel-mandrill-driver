<?php
namespace IGD\Mandrill\API;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class Api
{
    /**
     * The Guzzle client.
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * The mandrill configuration.
     *
     * @var array
     */
    private $config;

    /**
     * The rest object path.
     *
     * @var string
     */
    private $path;

    /**
     * Initialise the Api
     */
    public function __construct()
    {
        // Initalise the config
        $this->config = config('app.services.mandrill');

        $this->path = '/';

        // Initalise the guzzle client
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'base_uri' => 'https://mandrillapp.com/api/1.0/',
            'defaults' => [
                'query'  => [
                    'key' => $this->config['secret'],
                ],
            ]
        ]);
    }

    /**
     * Dynamically allow static calls of methods.
     *
     * @return self
     */
    public static function __callStatic($name, $arguments)
    {
        $class = get_called_class();
        $instance = new $class();
        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * Set the path of the api.
     *
     * @return self
     */
    protected function setPath(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Perform a request to the API service.
     *
     * @param string $method
     * @param array $params
     * @return object
     */
    protected function request(string $method, string $path, array $query = [], array $params = [], bool $auth = false)
    {
        $headers = [];

        // Perform request
        $response = $this->client->request($method, $this->path . $path . '.json', [
            'query' => $query,
            'json' => $params,
            'headers' => $headers,
        ]);
        $contents = (string) $response->getBody();
        return json_decode($contents);
    }

    /**
     * Perform a GET request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param bool $auth
     * @return object
     */
    protected function get(string $path, array $query = [], bool $auth = false)
    {
        return $this->request('GET', $path, $query, [], $auth);
    }

    /**
     * Perform a POST request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     */
    protected function post(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('POST', $path, $query, $params, $auth);
    }

    /**
     * Perform a PUT request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     */
    protected function put(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('PUT', $path, $query, $params, $auth);
    }

    /**
     * Perform a DELETE request to the API service.
     *
     * @param string $path
     * @param array $query
     * @param array $params
     * @param bool $auth
     * @return object
     */
    protected function delete(string $path, array $query = [], array $params = [], bool $auth = false)
    {
        return $this->request('DELETE', $path, $query, $params, $auth);
    }
}