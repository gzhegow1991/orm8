<?php

namespace Gzhegow\Orm\Core\Model\Traits\Grammar;

use Gzhegow\Orm\Core\Model\Scope\MariaDBGrammarScope;
use Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel;


/**
 * @mixin AbstractEloquentModel
 */
trait MariaDBGrammarTrait
{
    public function initializeMariaDBGrammarTrait()
    {
        static::$globalScopes[ MariaDBGrammarScope::class ] = new MariaDBGrammarScope();
    }
}
