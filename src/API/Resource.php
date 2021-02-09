<?php
namespace IGD\Mandrill\API;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class Resource implements Arrayable
{
    /**
     * Initalise the resource.
     *
     * @param array $data The resource data.
     */
    public function __construct($data = [])
    {
        $this->data($data);
    }

    /**
     * Set the data.
     *
     * @param  mixed  $data  The data.
     *
     * @return  self
     */
    public function data($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}