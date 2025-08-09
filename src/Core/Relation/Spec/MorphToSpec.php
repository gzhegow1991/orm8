<?php

namespace Gzhegow\Orm\Core\Relation\Spec;

use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @property string                               $relationName
 *
 * @property AbstractEloquentModel                $thisModel
 * @property AbstractEloquentModel                $morphModel
 * @property EloquentModelQueryBuilder            $morphModelQuery
 *
 * @property  string|null                         $morphType
 * @property  string|null                         $morphTypeKey
 * @property  string|null                         $morphIdKey
 *
 * @property  class-string<AbstractEloquentModel> $morphClass
 *
 * @property string|null                          $remoteTableLeftKey
 *
 * @property bool                                 $inverse
 */
class MorphToSpec extends AbstractSpec
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
    protected $morphModel = [];
    /**
     * @var EloquentModelQueryBuilder
     */
    protected $morphModelQuery = [];

    /**
     * @var string|null
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
     * @var class-string<AbstractEloquentModel>
     */
    protected $morphClass = [];

    /**
     * @var string|null
     */
    protected $remoteTableLeftKey = [];
}
