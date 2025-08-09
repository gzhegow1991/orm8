<?php

namespace Gzhegow\Orm\Core\Relation\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @template-covariant TModel of AbstractEloquentModel
 *
 * @mixin Relation
 * @mixin EloquentModelQueryBuilder
 */
interface RelationCanAssociateInterface extends RelationInterface
{
    /**
     * @param AbstractEloquentModel $model
     *
     * @return TModel
     */
    public function associate($model);

    /**
     * @return TModel
     */
    public function dissociate();
}
