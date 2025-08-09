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
 * @property string|null                                $thisTableRightKey
 * @property string|null                                $remoteTableLeftKey
 */
class BelongsToSpec extends AbstractSpec
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
     * @var string|null
     */
    protected $thisTableRightKey = [];
    /**
     * @var string|null
     */
    protected $remoteTableLeftKey = [];
}
