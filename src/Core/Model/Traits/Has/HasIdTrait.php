<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Gzhegow\Lib\Modules\Type\Ret;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 *
 * @property int|string $id
 */
trait HasIdTrait
{
    /**
     * @return int|string
     */
    public function getId()
    {
        $theType = Lib::type();

        $id = $this->attributes[ 'id' ] ?? null;

        $ret = Ret::new();

        $idValid = null
            ?? $theType->int_positive($id)->orNull($ret)
            ?? $theType->string_not_empty($id)->orNull($ret);

        $ret->orThrow();

        return $idValid;
    }

    /**
     * @param int|string $result
     */
    public function hasId(&$result = null) : bool
    {
        $result = null;

        $theType = Lib::type();

        $id = $this->attributes[ 'id' ] ?? null;

        $idValid = null
            ?? $theType->int_positive($id)->orNull()
            ?? $theType->string_not_empty($id)->orNull();

        if (null !== $idValid) {
            $result = $idValid;

            return true;
        }

        return false;
    }
}
