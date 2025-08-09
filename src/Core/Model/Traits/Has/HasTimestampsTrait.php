<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 *
 * @property \DateTimeInterface $created_at
 * @property \DateTimeInterface $updated_at
 */
trait HasTimestampsTrait
{
    public function freshTimestamp()
    {
        /** @see HasTimestamps::freshTimestamp() */

        return Lib::date()->idate_now();
    }
}
