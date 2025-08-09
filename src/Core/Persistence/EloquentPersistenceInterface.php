<?php

namespace Gzhegow\Orm\Core\Persistence;


use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder as EloquentPdoQueryBuilderBase;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Illuminate\Database\Eloquent\Builder as EloquentModelQueryBuilderBase;


interface EloquentPersistenceInterface
{
    public function reset() : void;


    public function persistBelongsToManyForSave(
        BelongsToMany $relation,
        AbstractEloquentModel $model, array $pivotAttributes = [], ?bool $touch = null
    ) : void;

    public function persistBelongsToManyForSaveMany(
        BelongsToMany $relation,
        $models, array $pivotAttributes = []
    ) : void;

    public function persistBelongsToManyForSync(
        BelongsToMany $relation,
        $ids, ?bool $detaching = null
    ) : void;


    public function persistHasOneOrManyForSave(
        HasOneOrMany $relation,
        AbstractEloquentModel $model
    ) : void;

    public function persistHasOneOrManyForSaveMany(
        HasOneOrMany $relation,
        $models
    ) : void;


    public function persistModelForSaveRecursive(AbstractEloquentModel $model) : void;

    public function persistModelForDeleteRecursive(AbstractEloquentModel $model) : void;


    public function persistModelForSave(AbstractEloquentModel $model) : void;

    public function persistModelForDelete(AbstractEloquentModel $model) : void;


    public function persistEloquentQueryForInsert(EloquentModelQueryBuilderBase $query, array $values) : void;

    public function persistEloquentQueryForUpdate(EloquentModelQueryBuilderBase $query, array $values) : void;

    public function persistEloquentQueryForDelete(EloquentModelQueryBuilderBase $query) : void;


    public function persistQueryForInsert(EloquentPdoQueryBuilderBase $query, array $values) : void;

    public function persistQueryForUpdate(EloquentPdoQueryBuilderBase $query, array $values) : void;

    public function persistQueryForDelete(EloquentPdoQueryBuilderBase $query) : void;


    public function persistSqlStatement(?Connection $conn, string $sql, array $bindings) : void;


    public function flush() : array;
}
