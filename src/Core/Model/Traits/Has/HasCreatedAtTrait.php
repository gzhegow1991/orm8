<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 *
 * @property \DateTimeInterface $created_at
 */
trait HasCreatedAtTrait
{
    public function setCreatedAt($createdAt) : void
    {
        $createdAtObject = $createdAt;

        if (null !== $createdAt) {
            $theType = Lib::type();

            $createdAtObject = $theType->idate($createdAt)->orThrow();
        }

        $this->attributes[ 'created_at' ] = $createdAtObject;
    }

    public function setupCreatedAt($createdAt = null) : string
    {
        $current = $this->attributes[ 'created_at' ] ?? null;

        if (null === $current) {
            $theDate = Lib::date();

            if (null === $createdAt) {
                $createdAtObject = $theDate->idate_now();

            } else {
                $createdAtObject = $theDate->type_idate($createdAt)->orThrow();
            }

            $this->attributes[ 'created_at' ] = $createdAtObject;
        }

        return $this->created_at;
    }
}
