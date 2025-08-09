<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Relations;

use Gzhegow\Orm\Core\Relation\Traits\HasRelationNameTrait;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationInterface;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationOneInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToBase;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationCanAssociateInterface;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @template-covariant TModel of AbstractEloquentModel
 */
class BelongsTo extends BelongsToBase implements
    RelationInterface,
    RelationOneInterface,
    RelationCanAssociateInterface
{
    use HasRelationNameTrait;


    /**
     * @var TModel
     */
    protected $child;


    /**
     * @param AbstractEloquentModel $model
     *
     * @return TModel
     */
    public function associate($model)
    {
        /** @see parent::associate() */

        $child = $this->child;

        if ($model) {
            $model->hasRawAttribute($this->ownerKey, $modelId);

            $child->setRawAttribute($this->foreignKey, $modelId ?? $model);
            $child->setRelation($this->relationName, $model);

        } else {
            $child->unsetRelation($this->relationName);
        }

        return $child;
    }


    /**
     * @return TModel
     */
    public function dissociate()
    {
        /** @see parent::dissociate() */

        $child = $this->child;

        $child->setAttribute($this->foreignKey, null);

        $child->setRelation($this->relationName, null);

        return $child;
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
