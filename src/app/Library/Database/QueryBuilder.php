<?php

namespace Backpack\CRUD\app\Library\Database;

use Illuminate\Database\Query\Builder;

class QueryBuilder extends Builder
{
    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        $query = base64_encode(json_encode([$this->toSql() => $this->getBindings()]));

        if (app('crud')->hasQuery($query)) {
            return app('crud')->getQuery($query);
        }

        $result = parent::runSelect();
        app('crud')->saveQuery($query, $result);

        return $result;
    }
}
