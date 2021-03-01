<?php
declare(strict_types=1);

namespace core\database\objects;

abstract class QueriesList
{

    private $queries;

    /**
     * @param string $id
     * @param Query $query
     */
    public function registerQuery(string $id, Query $query)
    {
        $this->queries[$id] = $query;
    }

    public function getQuery(string $id): ?Query
    {
        return $this->queries[$id] ?? null;
    }
}