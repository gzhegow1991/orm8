<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Relations;

use Gzhegow\Orm\Core\Orm;
use Gzhegow\Orm\Core\Relation\Traits\HasRelationNameTrait;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationInterface;
use Gzhegow\Orm\Core\Persistence\EloquentPersistenceInterface;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationOneInterface;
use Illuminate\Database\Eloquent\Relations\HasOne as HasOneBase;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


class HasOne extends HasOneBase implements
    RelationInterface,
    RelationOneInterface
{
    use HasRelationNameTrait;


    public function persistence() : EloquentPersistenceInterface
    {
        $persistence = Orm::persistence();

        return $persistence;
    }

    /**
     * @return static
     */
    public function persistForSave(AbstractEloquentModel $model)
    {
        $persistence = Orm::persistence();

        $persistence->persistHasOneOrManyForSave($this, $model);

        return $this;
    }

    /**
     * @return static
     */
    public function persistForSaveMany($models)
    {
        $persistence = Orm::persistence();

        $persistence->persistHasOneOrManyForSaveMany($this, $models);

        return $this;
    }
}
