<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Casts;

use Gzhegow\Lib\Lib;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;


class Base64SerializeCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $theFormatBaseN = Lib::formatBaseN();

        return unserialize($theFormatBaseN->base64_decode([], $value));
    }

    public function set($model, $key, $value, $attributes)
    {
        $theFormatBaseN = Lib::formatBaseN();

        return $theFormatBaseN->base64_encode([], serialize($value));
    }
}
