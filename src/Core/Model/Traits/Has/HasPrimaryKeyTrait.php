<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Type\Ret;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 */
trait HasPrimaryKeyTrait
{
    /**
     * @return int|string
     */
    public function getPrimaryKey()
    {
        $theType = Lib::type();

        $field = $this->getKeyName();

        $pk = $this->attributes[ $field ] ?? null;

        $ret = Ret::new();

        $pkValid = null
            ?? $theType->int_positive($pk)->orNull($ret)
            ?? $theType->string_not_empty($pk)->orNull($ret);

        $ret->orThrow();

        return $pkValid;
    }

    /**
     * @param null|int|string $result
     */
    public function hasPrimaryKey(&$result = null) : bool
    {
        $result = null;

        $theType = Lib::type();

        $field = $this->getKeyName();

        $pk = $this->attributes[ $field ] ?? null;

        $pkValid = null
            ?? $theType->int_positive($pk)->orNull()
            ?? $theType->string_not_empty($pk)->orNull();

        if (null !== $pkValid) {
            $result = $pkValid;

            return true;
        }

        return false;
    }
}
