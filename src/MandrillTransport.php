<?php

namespace LaravelMandrill;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Swift_Mime_SimpleMessage;

class MandrillTransport extends Transport
{
    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The Mandrill API key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new Mandrill transport instance.
     *
     * @param  \GuzzleHttp\ClientInterface  $client
     * @param  string  $key
     * @return void
     */
    public function __construct(ClientInterface $client, $key)
    {
        $this->key = $key;
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $response = $this->client->request('POST', 'https://mandrillapp.com/api/1.0/messages/send-raw.json', [
            'form_params' => [
                'key' => $this->key,
                'to' => $this->getTo($message),
                'raw_message' => $message->toString(),
                'async' => true,
            ],
        ]);

        $message->getHeaders()->addTextHeader(
            'X-Message-ID', $this->getMessageId($response)
        );

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get the message ID from the response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return string
     * @throws \JsonException
     */
    protected function getMessageId(ResponseInterface $response)
    {
        $response = json_decode((string) $response->getBody(), true);

        return Arr::get($response, '0._id');
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that Mandrill still respects CC, BCC headers in raw message itself.
     *
     * @param  \Swift_Mime_SimpleMessage $message
     * @return array
     */
    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        $to = [];

        if ($message->getTo()) {
            $to = array_merge($to, array_keys($message->getTo()));
        }

        if ($message->getCc()) {
            $to = array_merge($to, array_keys($message->getCc()));
        }

        if ($message->getBcc()) {
            $to = array_merge($to, array_keys($message->getBcc()));
        }

        return $to;
    }

    /**
     * Get the API key being used by the transport.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the API key being used by the transport.
     *
     * @param  string  $key
     * @return string
     */
    public function setKey($key)
    {
        return $this->key = $key;
    }
}
