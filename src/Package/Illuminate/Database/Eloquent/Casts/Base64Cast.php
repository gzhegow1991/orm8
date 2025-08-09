<?php

namespace Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Casts;

use Gzhegow\Lib\Lib;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;


class Base64Cast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        $theFormatBase64 = Lib::formatBase64();

        return $theFormatBase64->base64_decode([], $value);
    }

    public function set($model, $key, $value, $attributes)
    {
        $theFormatBase64 = Lib::formatBase64();

        return $theFormatBase64->base64_encode([], $value);
    }
}
