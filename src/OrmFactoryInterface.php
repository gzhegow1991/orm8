<?php

namespace Gzhegow\Orm;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Schema\Builder as EloquentSchemaBuilder;
use Gzhegow\Orm\Core\Query\Chunks\EloquentChunksProcessorInterface;
use Gzhegow\Orm\Package\Illuminate\Database\EloquentPdoQueryBuilder;
use Gzhegow\Orm\Core\Relation\Factory\EloquentRelationFactoryInterface;
use Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


interface OrmFactoryInterface
{
    public function newBuilder() : OrmBuilderInterface;


    public function newEloquentChunkProcessor() : EloquentChunksProcessorInterface;


    public function newEloquentRelationFactory(
        AbstractEloquentModel $model
    ) : EloquentRelationFactoryInterface;


    public function newEloquentSchemaBuilder(
        ConnectionInterface $connection
    ) : EloquentSchemaBuilder;

    public function newEloquentSchemaBlueprint(array $arguments) : EloquentSchemaBlueprint;


    public function newEloquentPdoQueryBuilder(
        ConnectionInterface $connection,
        ?Grammar $grammar = null,
        ?Processor $processor = null
    ) : EloquentPdoQueryBuilder;

    /**
     * @noinspection PhpDocSignatureInspection
     *
     * @template-covariant T of AbstractEloquentModel
     *
     * @param T $model
     *
     * @return EloquentModelQueryBuilder<T>
     */
    public function newEloquentModelQueryBuilder(
        EloquentPdoQueryBuilder $query,
        //
        AbstractEloquentModel $model
    ) : EloquentModelQueryBuilder;


    /**
     * @template-covariant T of AbstractEloquentModel
     *
     * @param iterable<T> $models
     *
     * @return EloquentModelCollection<T>
     */
    public function newEloquentModelCollection(
        iterable $models = []
    ) : EloquentModelCollection;
}
