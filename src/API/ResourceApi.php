<?php
namespace IGD\Mandrill\API;

use IGD\Mandrill\API\Resource;
use IGD\Mandrill\Query\Queryable;

abstract class ResourceApi extends Api implements Queryable
{
    /**
     * @var string
     */
    protected $resource;

    public function __construct(string $resource, string $path)
    {
        parent::__construct();
        $this->resource = $resource;
        $this->setPath($path);
    }

    /**
     * Find the item from the id.
     *
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function find(string $id, array $params = [])
    {
        $data = $this->post('info', array_merge(['id' => $id], $params));
        return (new $this->resource())->data($data);
    }
}