<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent;

use Gzhegow\Orm\Exception\Runtime\DeprecatedException;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


abstract class EloquentModel extends AbstractEloquentModel
{
    public function __toString() : string
    {
        // > originally, casting model/collection to string returns JSON, guess to posibility to store whole model to one DB cell
        // > it is deprecated magic that forces any string casting or dumping to do useless job
        throw new DeprecatedException('Casting model to string is deprecated');
    }


    public function __isset($key)
    {
        return $this->_exists($key);
    }

    public function __get($key)
    {
        return $this->_get($key);
    }

    public function __set($key, $value)
    {
        return $this->_set($key, $value);
    }

    public function __unset($key)
    {
        return $this->_unset($key);
    }


    public function offsetExists($offset) : bool
    {
        return $this->_exists($offset);
    }

    public function offsetGet($offset) : mixed
    {
        return $this->_get($offset);
    }

    public function offsetSet($offset, $value) : void
    {
        $this->_set($offset, $value);
    }

    public function offsetUnset($offset) : void
    {
        $this->offsetUnset($offset);
    }


    /**
     * > PHPStorm forget that the \JsonSerializable thanks to COMMUNITY!!11 requires `mixed` return type
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function jsonSerialize() : mixed
    {
        return $this->_jsonSerialize();
    }
}
