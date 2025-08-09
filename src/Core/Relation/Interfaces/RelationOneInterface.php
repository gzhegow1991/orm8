<?php

namespace Gzhegow\Orm\Core\Relation\Interfaces;

use Illuminate\Database\Eloquent\Relations\Relation;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @mixin Relation
 * @mixin EloquentModelQueryBuilder
 */
interface RelationOneInterface extends RelationInterface
{
}
