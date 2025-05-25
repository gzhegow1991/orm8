<?php

namespace Gzhegow\Orm\Core;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Gzhegow\Orm\Core\Query\Chunks\ChunksProcessor;
use Illuminate\Database\Query\Processors\Processor;
use Gzhegow\Orm\Core\Query\Chunks\ChunksProcessorInterface;
use Illuminate\Database\Schema\Builder as EloquentSchemaBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\EloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\EloquentPdoQueryBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


class OrmFactory implements OrmFactoryInterface
{
    public function newChunkProcessor() : ChunksProcessorInterface
    {
        return new ChunksProcessor();
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
     * @template-covariant T of EloquentModel
     *
     * @param T $model
     *
     * @return EloquentModelQueryBuilder<T>
     */
    public function newEloquentModelQueryBuilder(
        EloquentPdoQueryBuilder $query,
        //
        EloquentModel $model
    ) : EloquentModelQueryBuilder
    {
        return new EloquentModelQueryBuilder(
            $query,
            //
            $model
        );
    }


    /**
     * @template-covariant T of EloquentModel
     *
     * @param iterable<T> $models
     *
     * @return EloquentModelCollection<T>|T[]
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
