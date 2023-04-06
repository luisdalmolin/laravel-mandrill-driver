<?php

namespace LaravelMandrill;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use MailchimpTransactional\ApiClient;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mime\RawMessage;
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
    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        // Set headers must take place before SentMessage is formed or it will not be part
        // of the payload submitted to the Mandrill API.
        $message = $this->setHeaders($message);
        
        return parent::send($message, $envelope);
    }

    /**
     * {@inheritDoc}
     */
    protected function doSend(SentMessage $message): void
    {
        $data = $this->mailchimp->messages->sendRaw([
            'raw_message' => $message->toString(),
            'async' => true,
            'to' => $this->getTo($message),
        ]);

        // If Mandrill _id was returned, set it as the message id for
        // use elsewhere in the application.
        if (!empty($data[0]?->_id)) {
            $messageId = $data[0]->_id;
            $message->setMessageId($messageId);
            // Convention seems to be to set this header on the original for access later.
            $message->getOriginalMessage()->getHeaders()->addHeader('X-Message-ID', $messageId);
        }
    }

    /**
     * Retrieves recipients from the original message or envelope.
     *
     * @param SentMessage $message
     * @return array
     */
    protected function getTo(SentMessage $message): array
    {
        $recipients = [];

        $original_message = $message->getOriginalMessage();

        if ($original_message instanceof Email) {

            if (!empty($original_message->getTo())) {
                foreach ($original_message->getTo() as $to) {
                    $recipients[] = $to->getEncodedAddress();
                }
            }

            if (!empty($original_message->getCc())) {
                foreach ($original_message->getCc() as $cc) {
                    $recipients[] = $cc->getEncodedAddress();
                }
            }

            if (!empty($original_message->getBcc())) {
                foreach ($original_message->getBcc() as $bcc) {
                    $recipients[] = $bcc->getEncodedAddress();
                }
            }
        }

        // Fall-back to envelope recipients
        if (empty($recipients)) {
            foreach ($message->getEnvelope()->getRecipients() as $recipient) {
                $recipients[] = $recipient->getEncodedAddress();
            }
        }

        return $recipients;
    }

    /**
     * Set headers of email.
     *
     * @param Message $message
     *
     * @return Message
     */
    protected function setHeaders(Message $message): Message
    {   
        $messageHeaders = $message->getHeaders();
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

    /**
     * Replace Mandrill client.
     * This is used primarily for testing but could in theory allow other use cases
     * e.g. Configuring proxying in Guzzle.
     * 
     * @param ApiClient $client [description]
     * @return void
     */
    public function setClient(ApiClient $client): void
    {
        $this->mailchimp = $client;
    }
}
