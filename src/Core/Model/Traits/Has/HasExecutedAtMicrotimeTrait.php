<?php

namespace Gzhegow\Orm\Core\Model\Traits\Has;

use Gzhegow\Lib\Lib;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 *
 * @property string $executed_at_microtime
 */
trait HasExecutedAtMicrotimeTrait
{
    public function setExecutedAtMicrotime($executedAtMicrotime) : void
    {
        $executedAtMicrotimeString = $executedAtMicrotime;

        if (null !== $executedAtMicrotimeString) {
            $theDate = Lib::date();

            $executedAtMicrotimeString = $theDate->type_idate_microtime($executedAtMicrotime)->orThrow();

            $executedAtMicrotimeString = $theDate->format_usec($executedAtMicrotimeString);
        }

        $this->attributes[ 'executed_at_microtime' ] = $executedAtMicrotimeString;
    }

    public function setupExecutedAtMicrotime($executedAtMicrotime = null) : string
    {
        $current = $this->attributes[ 'executed_at_microtime' ] ?? null;

        if (null === $current) {
            $theDate = Lib::date();

            if (null === $executedAtMicrotime) {
                $executedAtMicrotimeString = $theDate->idate_now();

            } else {
                $executedAtMicrotimeString = $theDate->type_idate_microtime($executedAtMicrotime)->orThrow();
            }

            $executedAtMicrotimeString = $theDate->format_usec($executedAtMicrotimeString);

            $this->attributes[ 'executed_at_microtime' ] = $executedAtMicrotimeString;
        }

        return $this->executed_at_microtime;
    }
}
