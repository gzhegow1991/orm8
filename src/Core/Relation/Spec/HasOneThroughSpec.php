<?php

namespace Gzhegow\Orm\Core\Relation\Spec;

use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @property string                                     $relationName
 *
 * @property AbstractEloquentModel                      $thisModel
 * @property AbstractEloquentModel                      $throughModel
 * @property AbstractEloquentModel                      $remoteModel
 * @property EloquentModelQueryBuilder                  $remoteModelQuery
 *
 * @property string|class-string<AbstractEloquentModel> $remoteModelClassOrTableName
 * @property class-string<AbstractEloquentModel>        $throughModelClass
 *
 * @property string|null                                $thisTableRightKey
 * @property string|null                                $throughTableLeftKey
 * @property string|null                                $throughTableRightKey
 * @property string|null                                $remoteTableLeftKey
 */
class HasOneThroughSpec extends AbstractSpec
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
    protected $throughModel = [];
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
     * @var class-string<AbstractEloquentModel>
     */
    protected $throughModelClass = [];

    /**
     * @var string|null
     */
    protected $thisTableRightKey = [];
    /**
     * @var string|null
     */
    protected $throughTableLeftKey = [];
    /**
     * @var string|null
     */
    protected $throughTableRightKey = [];
    /**
     * @var string|null
     */
    protected $remoteTableLeftKey = [];
}
