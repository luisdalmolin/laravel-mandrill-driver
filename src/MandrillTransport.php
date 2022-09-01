<?php

namespace LaravelMandrill;

use MailchimpTransactional\ApiClient;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class MandrillTransport extends AbstractTransport
{
    /**
     * Create a new Mandrill transport instance.
     *
     * @param ApiClient $mailchimp
     * @param array $headers
     */
    public function __construct(protected ApiClient $mailchimp, protected array $headers)
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $message = $this->setHeaders($message);

        $this->mailchimp->messages->sendRaw([
            'raw_message' => $message->toString(),
            'async' => true,
        ]);
    }

    /**
     * Set headers of email.
     *
     * @param SentMessage  $message
     *
     * @return SentMessage
     */
    protected function setHeaders(SentMessage $message): SentMessage
    {
        $messageHeaders = $message->getOriginalMessage()->getHeaders();

        $messageHeaders->addTextHeader('X-Dump', 'dumpy');

        foreach ($this->headers as $name => $value) {
            $messageHeaders->addTextHeader($name, $value);
        }

        return $message;
    }

    /**
     * Get the string representation of the transport.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'mandrill';
    }
}
