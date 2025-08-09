<?php

namespace Gzhegow\Orm\Core\Query\ModelQuery\Traits;

use Gzhegow\Orm\Core\Query\Chunks\EloquentChunksBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelQueryBuilder;


/**
 * @template-covariant T of AbstractEloquentModel
 *
 * @mixin EloquentModelQueryBuilder
 */
trait ChunksTrait
{
    /**
     * @return EloquentChunksBuilder
     */
    public function chunks() : EloquentChunksBuilder
    {
        return EloquentChunksBuilder::fromModelQuery($this)->orThrow();
    }


    /**
     * @return \Generator<EloquentModelCollection<T>>
     */
    public function chunksModelNativeForeach(
        int $limitChunk, ?int $limit = null,
        ?int $offset = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->chunksModelNativeForeach(
                $limitChunk, $limit,
                $offset
            )
        ;

        $generator = $builder->chunksForeach();

        return $generator;
    }

    /**
     * @return \Generator<EloquentModelCollection<T>>
     */
    public function chunksModelAfterForeach(
        int $limitChunk, ?int $limit = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->chunksModelAfterForeach(
                $limitChunk, $limit,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        $generator = $builder->chunksForeach();

        return $generator;
    }


    /**
     * @return \Generator<EloquentModelCollection<T>>
     */
    public function chunksPdoNativeForeach(
        int $limitChunk, ?int $limit = null,
        ?int $offset = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->chunksPdoNativeForeach(
                $limitChunk, $limit,
                $offset
            )
        ;

        $generator = $builder->chunksForeach();

        return $generator;
    }

    /**
     * @return \Generator<EloquentModelCollection<T>>
     */
    public function chunksPdoAfterForeach(
        int $limitChunk, ?int $limit = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->chunksPdoAfterForeach(
                $limitChunk, $limit,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        $generator = $builder->chunksForeach();

        return $generator;
    }


    public function paginateModelNativeForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?int $offset = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->paginateModelNativeForeach(
                $perPage, $page, $pagesDelta,
                $offset
            )
        ;

        return $builder;
    }

    public function paginateModelAfterForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->paginateModelAfterForeach(
                $perPage, $page, $pagesDelta,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        return $builder;
    }


    public function paginatePdoNativeForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?int $offset = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->paginatePdoNativeForeach(
                $perPage, $page, $pagesDelta,
                $offset
            )
        ;

        return $builder;
    }

    public function paginatePdoAfterForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelQuery($this)->orThrow();

        $builder
            ->paginatePdoAfterForeach(
                $perPage, $page, $pagesDelta,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        return $builder;
    }
}
