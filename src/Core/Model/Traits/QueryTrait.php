<?php

namespace Gzhegow\Orm\Core\Model\Traits;

use Illuminate\Database\ConnectionInterface;
use Gzhegow\Orm\Package\Illuminate\Database\EloquentPdoQueryBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\EloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @mixin EloquentModel
 */
trait QueryTrait
{
    public function connectionThis() : ConnectionInterface
    {
        $connection = $this->getConnection();

        return $connection;
    }

    public static function connection() : ConnectionInterface
    {
        $model = static::getModel();

        $connection = $model->connectionThis();

        return $connection;
    }


    /**
     * @return EloquentModelQueryBuilder<static>
     */
    public function queryThis()
    {
        $query = $this->newQuery();

        return $query;
    }

    /**
     * @return EloquentModelQueryBuilder<static>
     */
    public static function query()
    {
        $model = static::getModel();

        $query = $model->queryThis();

        return $query;
    }


    /**
     * @return EloquentModelQueryBuilder<static>
     */
    public function queryWhereThis(...$where)
    {
        $query = $this->newQuery();

        $query->where(...$where);

        return $query;
    }

    /**
     * @return EloquentModelQueryBuilder<static>
     */
    public static function queryWhere(...$where)
    {
        $model = static::getModel();

        $query = $model->queryWhereThis(...$where);

        return $query;
    }


    /**
     * @return EloquentPdoQueryBuilder
     */
    public function queryStdThis()
    {
        $query = $this->newQuery();

        $queryPdo = $query->getQuery();

        return $queryPdo;
    }

    /**
     * @return EloquentPdoQueryBuilder
     */
    public static function queryStd()
    {
        $model = static::getModel();

        $query = $model->queryStdThis();

        return $query;
    }


    /**
     * @return EloquentPdoQueryBuilder
     */
    public function queryStdKeysThis()
    {
        $query = $this->newQuery();

        $queryPdo = $query->getQuery();
        $queryPdo->select($this->getKeyName());

        return $queryPdo;
    }

    /**
     * @return EloquentPdoQueryBuilder
     */
    public static function queryStdKeys()
    {
        $model = static::getModel();

        $query = $model->queryStdKeysThis();

        return $query;
    }


    /**
     * @return EloquentPdoQueryBuilder
     */
    public function queryStdWhereThis(...$where)
    {
        $query = $this->newQuery();

        $queryPdo = $query->getQuery();

        $queryPdo->where(...$where);

        return $queryPdo;
    }

    /**
     * @return EloquentPdoQueryBuilder
     */
    public static function queryStdWhere(...$where)
    {
        $model = static::getModel();

        $queryPdo = $model->queryStdWhereThis(...$where);

        return $queryPdo;
    }


    /**
     * @return EloquentPdoQueryBuilder
     */
    public function queryStdKeysWhereThis(...$where)
    {
        $query = $this->newQuery();

        $queryPdo = $query->getQuery();
        $queryPdo->select($this->getKeyName());

        $queryPdo->where(...$where);

        return $queryPdo;
    }

    /**
     * @return EloquentPdoQueryBuilder
     */
    public static function queryStdKeysWhere(...$where)
    {
        $model = static::getModel();

        $queryPdo = $model->queryStdKeysWhereThis(...$where);

        return $queryPdo;
    }
}
