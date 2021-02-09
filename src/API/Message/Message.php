<?php
namespace IGD\Mandrill\API\Message;

use IGD\Mandrill\API\Resource;

class Message extends Resource
{
    /**
     * Load the message information.
     *
     * @return self
     */
    public function load()
    {
        return $this->data(Mandrill::message()->find($this->_id));
    }

    /**
     * Get the content.
     *
     * @return mixed
     */
    public function content()
    {
        return Mandrill::message()->content($this->_id);
    }
}