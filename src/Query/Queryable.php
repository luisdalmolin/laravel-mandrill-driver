<?php
namespace IGD\Mandrill\Query;

use Illuminate\Support\Collection;

interface Queryable
{
    /**
     * Perform the query and get the results.
     *
     * @param array $query
     * @param bool $search
     * @return \Illuminate\Support\Collection
     */
    public function perform(array $query, bool $search = false): Collection;
}