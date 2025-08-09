<?php

namespace Gzhegow\Orm\Core\Model\Traits;

use Gzhegow\Lib\Lib;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 */
trait DateTrait
{
    protected function asDate($value)
    {
        /** @see HasAttributes::asDate() */

        $date = $this->asDateTime($value);

        $date->setTime(0, 0, 0, 0);

        return $date;
    }


    /**
     * @return \DateTimeImmutable
     */
    protected function asDateTime($value)
    {
        /** @see HasAttributes::asDateTime() */

        if (null === $value) {
            return null;
        }

        $theType = Lib::type();

        $formats = [
            'Y-m-d H:i:s.u',
            'Y-m-d H:i:s.v',
            'Y-m-d H:i:s',
            $this->getDateFormat(),
        ];

        $iDate = $theType->idate_formatted($value, $formats)->orThrow();

        return $iDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    protected function asDateTimeFormat($value, string $format)
    {
        return Lib::type()->idate_formatted($value, [ $format ])->orThrow();
    }


    protected function serializeDate(\DateTimeInterface $date)
    {
        /** @see HasAttributes::serializeDate() */

        return Lib::date()->format_javascript_msec($date);
    }
}
