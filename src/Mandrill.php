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
}