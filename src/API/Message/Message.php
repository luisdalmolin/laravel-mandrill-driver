<?php
namespace IGD\Mandrill\API\Message;

use IGD\Mandrill\API\Resource;

class Message extends Resource
{
    /**
     * The message id.
     *
     * @var int
     */
    public $id = null;

    /**
     * Initialise the message with a message id.
     *
     * @param string $messageId
     */
    public function __construct(string $messageId)
    {
        parent::__construct();
        $this->id = $messageId;
    }

    /**
     * Load the message information.
     *
     * @return self
     */
    public function load()
    {
        return $this->data((new MessageApi())->find($this->id));
    }

    /**
     * Get the content.
     *
     * @return mixed
     */
    public function content()
    {
        return (new MessageApi())->content($this->id);
    }
}