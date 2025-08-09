<?php

namespace Gzhegow\Orm\Core\Relation\Spec;

use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @property string                                     $relationName
 *
 * @property AbstractEloquentModel                      $thisModel
 * @property AbstractEloquentModel                      $remoteModel
 * @property EloquentModelQueryBuilder                  $remoteModelQuery
 *
 * @property string|class-string<AbstractEloquentModel> $remoteModelClassOrTableName
 *
 * @property string|null                                $morphType
 * @property string|null                                $morphTypeKey
 * @property string|null                                $morphIdKey
 *
 * @property string|null                                $thisTableRightKey
 */
class MorphManySpec extends AbstractSpec
{
    /**
     * @var string
     */
    protected $relationName = [];

    /**
     * @var AbstractEloquentModel
     */
    protected $thisModel = [];
    /**
     * @var AbstractEloquentModel
     */
    protected $remoteModel = [];
    /**
     * @var EloquentModelQueryBuilder
     */
    protected $remoteModelQuery = [];

    /**
     * @var string|class-string<AbstractEloquentModel>
     */
    protected $remoteModelClassOrTableName = [];

    /**
     * @var string
     */
    protected $morphType = [];
    /**
     * @var string|null
     */
    protected $morphTypeKey = [];
    /**
     * @var string|null
     */
    protected $morphIdKey = [];

    /**
     * @var string|null
     */
    protected $thisTableRightKey = [];
}
