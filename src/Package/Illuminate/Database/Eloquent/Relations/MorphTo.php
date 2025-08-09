<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Relations;

use Gzhegow\Orm\Core\Relation\Traits\HasRelationNameTrait;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationInterface;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationOneInterface;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToBase;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationCanAssociateInterface;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @template-covariant TModel of AbstractEloquentModel
 */
class MorphTo extends MorphToBase implements
    RelationInterface,
    RelationOneInterface,
    RelationCanAssociateInterface
{
    use HasRelationNameTrait;


    /**
     * @var TModel
     */
    protected $parent;


    /**
     * @param AbstractEloquentModel $model
     *
     * @return TModel
     */
    public function associate($model)
    {
        /** @see parent::associate() */

        $parent = $this->parent;

        if ($model) {
            $modelMorphClass = $model->getMorphClass();

            $model->hasRawAttribute($this->ownerKey, $modelId);

            $parent->setRawAttribute($this->foreignKey, $modelId ?? $model);
            $parent->setRawAttribute($this->morphType, $modelMorphClass);
            $parent->setRelation($this->relationName, $model);

        } else {
            $parent->unsetRelation($this->relationName);
        }

        return $parent;
    }

    /**
     * @return TModel
     */
    public function dissociate()
    {
        /** @see parent::dissociate() */

        $parent = $this->parent;

        $parent->setAttribute($this->foreignKey, null);

        $parent->setAttribute($this->morphType, null);

        $parent->setRelation($this->relationName, null);

        return $parent;
    }


    public function addConstraints()
    {
        /** @see parent::addConstraints() */

        if (static::$constraints) {
            $table = $this->related->getTable();

            $this->query->where(
                $table . '.' . $this->ownerKey,
                '=',
                $this->child->getAttribute($this->foreignKey)
            );
        }
    }
}
