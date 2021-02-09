<?php
namespace IGD\Mandrill\API\Message;

use IGD\Mandrill\API\Message\Message;
use IGD\Mandrill\API\Category\Category;
use IGD\Mandrill\API\ResourceApi;
use Illuminate\Support\Collection;

class MessageApi extends ResourceApi
{
    /**
     * Initialise the message api
     */
    public function __construct()
    {
        parent::__construct(Message::class, 'messages');
    }

    /**
     * Perform the query and get the results.
     *
     * @param array $query
     * @param bool $search
     * @return \Illuminate\Support\Collection
     */
    public function perform(array $query, bool $search = false): Collection
    {
        if (!$search) {
            return collect();
        }

        $response = $this->get('search', $query);
        return collect($response)->map(function ($message) {
            return (new Message())->data($message);
        });
    }

    /**
     * Send a message.
     *
     * @param array $data
     * @return mixed
     */
    public function send(array $data)
    {
        return $this->post('send', [], ['message' => $data]);
    }

    /**
     * Send a template message.
     *
     * @param string $template
     * @param array $content
     * @param array $message
     * @return mixed
     */
    public function sendTemplate(string $template, array $content = [], array $message = [])
    {
        return $this->post('send-template', [], [
            'template_name' => $template,
            'template_content' => $content,
            'message' => $message,
        ]);
    }

    /**
     * Send a raw message.
     *
     * @param array $to
     * @param string $message
     * @param bool $async
     * @return mixed
     */
    public function sendRaw(array $to, string $message, bool $async = true)
    {
        return $this->post('send-raw', [], [
            'to' => $to,
            'raw_message' => $message,
            'async' => $async,
        ]);
    }

    /**
     * Get the message content.
     *
     * @param string $messageId
     * @return mixed
     */
    public function content(string $messageId)
    {
        return $this->get('content', ['id' => $messageId]);
    }
}