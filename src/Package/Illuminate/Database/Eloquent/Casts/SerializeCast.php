<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Casts;

use Gzhegow\Lib\Lib;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;


class SerializeCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $theFormatSerialize = Lib::formatSerialize();

        return $theFormatSerialize->unserialize([], $value);
    }

    public function set($model, $key, $value, $attributes)
    {
        $theFormatSerialize = Lib::formatSerialize();

        return $theFormatSerialize->serialize([], $value);
    }
}
