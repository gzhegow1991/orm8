<?php

namespace Gzhegow\Orm\Core\Model\Traits;

use Gzhegow\Orm\Core\Query\Chunks\EloquentChunksBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection;


/**
 * @mixin AbstractEloquentModel
 */
trait ChunksTrait
{
    /**
     * @return EloquentChunksBuilder
     */
    public static function chunks() : EloquentChunksBuilder
    {
        return EloquentChunksBuilder::fromModelClass(static::class)->orThrow();
    }


    /**
     * @return \Generator<EloquentModelCollection<static>>
     */
    public static function chunksModelNativeForeach(
        int $limitChunk, ?int $limit = null,
        ?int $offset = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

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
     * @return \Generator<EloquentModelCollection<static>>
     */
    public static function chunksModelAfterForeach(
        int $limitChunk, ?int $limit = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

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
     * @return \Generator<EloquentModelCollection<static>>
     */
    public static function chunksPdoNativeForeach(
        int $limitChunk, ?int $limit = null,
        ?int $offset = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

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
     * @return \Generator<EloquentModelCollection<static>>
     */
    public static function chunksPdoAfterForeach(
        int $limitChunk, ?int $limit = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : \Generator
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

        $builder
            ->chunksPdoAfterForeach(
                $limitChunk, $limit,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        $generator = $builder->chunksForeach();

        return $generator;
    }


    public static function paginateModelNativeForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?int $offset = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

        $builder
            ->paginateModelNativeForeach(
                $perPage, $page, $pagesDelta,
                $offset
            )
        ;

        return $builder;
    }

    public static function paginateModelAfterForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

        $builder
            ->paginateModelAfterForeach(
                $perPage, $page, $pagesDelta,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        return $builder;
    }


    public static function paginatePdoNativeForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?int $offset = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

        $builder
            ->paginatePdoNativeForeach(
                $perPage, $page, $pagesDelta,
                $offset
            )
        ;

        return $builder;
    }

    public static function paginatePdoAfterForeach(
        ?int $perPage = null, ?int $page = null, ?int $pagesDelta = null,
        ?string $offsetColumn = null, ?string $offsetOperator = null, $offsetValue = null, ?bool $includeOffsetValue = null
    ) : EloquentChunksBuilder
    {
        $builder = EloquentChunksBuilder::fromModelClass(static::class)->orThrow();

        $builder
            ->paginatePdoAfterForeach(
                $perPage, $page, $pagesDelta,
                $offsetColumn, $offsetOperator, $offsetValue, $includeOffsetValue
            )
        ;

        return $builder;
    }
}
