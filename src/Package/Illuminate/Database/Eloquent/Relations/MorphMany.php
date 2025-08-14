<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Relations;

use Gzhegow\Orm\Orm;
use Gzhegow\Orm\Core\Relation\Traits\HasRelationNameTrait;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationInterface;
use Gzhegow\Orm\Core\Persistence\EloquentPersistenceInterface;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationManyInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany as MorphManyBase;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


class MorphMany extends MorphManyBase implements
    RelationInterface,
    RelationManyInterface
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
        $persistence = $this->persistence();

        $persistence->persistHasOneOrManyForSave($this, $model);

        return $this;
    }

    /**
     * @return static
     */
    public function persistForSaveMany($models)
    {
        $persistence = $this->persistence();

        $persistence->persistHasOneOrManyForSaveMany($this, $models);

        return $this;
    }
}
