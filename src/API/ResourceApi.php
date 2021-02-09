<?php
namespace IGD\Mandrill\API;

use IGD\Mandrill\API\Api;
use IGD\Mandrill\Query\Queryable;

abstract class ResourceApi extends Api implements Queryable
{
    /**
     * Find the item from the id.
     *
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function find(string $id, array $params = [])
    {
        return $this->get('info', array_merge(['id' => $id], $params));
    }
}