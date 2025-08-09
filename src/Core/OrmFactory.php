<?php

namespace Gzhegow\Orm\Core;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Gzhegow\Orm\Core\Query\Chunks\EloquentChunksProcessor;
use Gzhegow\Orm\Core\Relation\Factory\EloquentRelationFactory;
use Illuminate\Database\Schema\Builder as EloquentSchemaBuilder;
use Gzhegow\Orm\Core\Query\Chunks\EloquentChunksProcessorInterface;
use Gzhegow\Orm\Package\Illuminate\Database\EloquentPdoQueryBuilder;
use Gzhegow\Orm\Core\Relation\Factory\EloquentRelationFactoryInterface;
use Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


class OrmFactory implements OrmFactoryInterface
{
    public function newBuilder() : OrmBuilderInterface
    {
        return new OrmBuilder($this);
    }


    public function newEloquentChunkProcessor() : EloquentChunksProcessorInterface
    {
        return new EloquentChunksProcessor();
    }


    public function newEloquentRelationFactory(
        AbstractEloquentModel $model
    ) : EloquentRelationFactoryInterface
    {
        return new EloquentRelationFactory($model);
    }


    public function newEloquentSchemaBuilder(
        ConnectionInterface $connection
    ) : EloquentSchemaBuilder
    {
        $schema = $connection->getSchemaBuilder();

        $schema->blueprintResolver(
            function (...$arguments) use ($connection) {
                $options = [
                    'connection' => $connection,
                ];

                $blueprint = $this->newEloquentSchemaBlueprint(
                    $arguments,
                    $options
                );

                return $blueprint;
            }
        );

        return $schema;
    }

    public function newEloquentSchemaBlueprint(
        array $arguments,
        array $options = []
    ) : EloquentSchemaBlueprint
    {
        return new EloquentSchemaBlueprint(
            $this,
            //
            $arguments,
            $options
        );
    }


    public function newEloquentPdoQueryBuilder(
        ConnectionInterface $connection,
        ?Grammar $grammar = null,
        ?Processor $processor = null
    ) : EloquentPdoQueryBuilder
    {
        return new EloquentPdoQueryBuilder(
            $connection,
            $grammar,
            $processor
        );
    }

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
    ) : EloquentModelQueryBuilder
    {
        return new EloquentModelQueryBuilder(
            $query,
            //
            $model
        );
    }


    /**
     * @template-covariant T of AbstractEloquentModel
     *
     * @param iterable<T> $models
     *
     * @return EloquentModelCollection<T>
     */
    public function newEloquentModelCollection(
        iterable $models = []
    ) : EloquentModelCollection
    {
        $items = [];

        foreach ( $models as $i => $model ) {
            $items[ $i ] = $model;
        }

        return new EloquentModelCollection($items);
    }
}
