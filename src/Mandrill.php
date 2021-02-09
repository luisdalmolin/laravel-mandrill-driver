<?php
namespace IGD\Mandrill;

use IGD\Mandrill\API\Message\MessageApi;
use IGD\Mandrill\Query\Builder;

class Mandrill
{
    /**
     * Get the message query builder.
     *
     * @return \IGD\Mandrill\Query\Builder
     */
    public function messages(): Builder
    {
        return new Builder(new MessageApi());
    }

    /**
     * Get the message api.
     *
     * @return \IGD\Mandrill\API\Message\MessageApi
     */
    public function message(): MessageApi
    {
        return new MessageApi();
    }
}