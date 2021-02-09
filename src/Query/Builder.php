<?php
namespace IGD\Mandrill\Query;

use IGD\Mandrill\Query\Queryable;
use Illuminate\Support\Collection;

class Builder
{
    /**
     * The queryable resource.
     *
     * @var \IGD\Mandrill\Query\Queryable
     */
    private $queryable;

    /**
     * The where conditions to restrict the results.
     *
     * @var array
     */
    private $where = [];

    /**
     * The number of items to pull back per page.
     *
     * @var null|int
     */
    private $limit = null;

    /**
     * Initialise the builder with a queryable resource.
     *
     * @param \IGD\Mandrill\Query\Queryable $queryable
     */
    public function __construct(Queryable $queryable)
    {
        $this->queryable = $queryable;
    }

    /**
     * Build the query.
     *
     * @return array
     */
    private function build(): array
    {
        $query = [];

        // Append where conditions
        if (!empty($this->where)) {
            foreach ($this->where as $key => $value) {
                // Convert boolean value to string
                if (is_bool($value)) {
                    $value = $value ? 'true' : 'false';
                }

                // Handle date time format
                if ($value instanceof \DateTime) {
                    $value = $value->format('Y-m-d');
                }

                // Set key / value
                $query[$key] = $value;
            }
        }

        // Append limit
        if (!empty($this->limit)) {
            $query['perPage'] = $this->limit;
        }

        return $query;
    }

    /**
     * Get the queried items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(): Collection
    {
        return $this->queryable->perform($this->build());
    }

    /**
     * Search the queried items.
     *
     * @param string $value
     * @return \Illuminate\Support\Collection
     */
    public function search(string $value): Collection
    {
        $query = $this->build();
        $query['query'] = $value;
        return $this->queryable->perform($query, true);
    }

    /**
     * Find the item.
     *
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function find(string $id, array $params = [])
    {
        return $this->queryable->find($id, $params);
    }

    /**
     * Get all the items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return $this->limit(null)->get();
    }

    /**
     * Get the first item.
     *
     * @return mixed
     */
    public function first()
    {
        return $this->limit(1)->get()->first();
    }

    /**
     * Restrict the query by a where condition.
     *
     * @param string $field
     * @param string|array $value
     * @return \IGD\Mandrill\Query\Builder
     */
    public function where(string $field, $value): Builder
    {
        $this->where[$field] = $value;
        return $this;
    }

    /**
     * Set the limit.
     *
     * @param null|int $limit
     * @return \IGD\Mandrill\Query\Builder
     */
    public function limit(?int $limit): Builder
    {
        // Handle boundaries
        if ($limit < 1 || $limit > 100) {
            $limit = null;
        }

        $this->limit = $limit;
        return $this;
    }
}