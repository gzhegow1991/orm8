<?php

namespace Gzhegow\Orm\Core\Relation\Traits;

use Gzhegow\Orm\Exception\LogicException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Gzhegow\Orm\Package\Illuminate\Database\Capsule\Eloquent;
use Gzhegow\Orm\Core\Relation\Interfaces\RelationInterface;


/**
 * @mixin Relation
 * @mixin RelationInterface
 */
trait HasRelationNameTrait
{
    /**
     * @var string
     */
    protected $relationName;


    public function getRelationName() : string
    {
        return $this->relationName;
    }

    /**
     * @return static
     */
    public function setRelationName(?string $relationName)
    {
        if ('' === $relationName) {
            throw new LogicException(
                [ 'The `relationName` should be non-empty string' ]
            );
        }

        $relationPrefix = Eloquent::getRelationPrefix();

        if ('' !== $relationPrefix) {
            if (0 !== strpos($relationName, $relationPrefix)) {
                throw new LogicException(
                    [ 'The `relationName` should start with `relationPrefix`: ' . $relationName, $relationPrefix ]
                );
            }
        }

        $this->relationName = $relationName;

        return $this;
    }
}
