<?php

namespace LaravelMandrill;

use MailchimpTransactional\ApiClient;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

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
            'to' => $this->getTo($message),
        ]);
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
