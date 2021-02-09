<?php
namespace IGD\Mandrill;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;
use Swift_Mime_SimpleMessage;

class Transport extends \Illuminate\Mail\Transport\Transport
{
    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $response = Mandrill::message()->sendRaw($this->getTo($message), $message->toString());

        $message->getHeaders()->addTextHeader(
            'X-Message-ID', $response[0]->_id,
        );

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
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
}
