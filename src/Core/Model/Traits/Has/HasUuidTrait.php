<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 *
 * @property string $uuid
 */
trait HasUuidTrait
{
    public function getUuid() : string
    {
        $theType = Lib::type();

        $uuid = $this->attributes[ 'uuid' ] ?? null;

        $uuidValid = $theType->uuid($uuid)->orThrow();

        return $uuidValid;
    }

    public function hasUuid(&$result = null) : bool
    {
        $result = null;

        $theType = Lib::type();

        $uuid = $this->attributes[ 'uuid' ] ?? null;

        $uuidValid = $theType->uuid($uuid)->orNull();

        if (null !== $uuidValid) {
            $result = $uuidValid;

            return true;
        }

        return false;
    }


    public function setUuid($uuid) : void
    {
        $uuidValue = Lib::type()->uuid($uuid)->orThrow();

        $this->attributes[ 'uuid' ] = $uuidValue;
    }

    public function setupUuid($uuid = null) : string
    {
        $current = $this->attributes[ 'uuid' ] ?? null;

        if (null === $current) {
            if (null !== $uuid) {
                $uuidValue = Lib::type()->uuid($uuid)->orThrow();

            } else {
                $uuidValue = Lib::random()->uuid();
            }

            $this->attributes[ 'uuid' ] = $uuidValue;
        }

        return $this->attributes[ 'uuid' ];
    }
}
