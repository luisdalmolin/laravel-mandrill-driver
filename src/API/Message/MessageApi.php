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
        parent::__construct();
        $this->setPath('messages');
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