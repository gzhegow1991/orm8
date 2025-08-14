<?php

namespace Gzhegow\Orm\Core\Orm;

use Gzhegow\Lib\Modules\Type\Ret;
use Gzhegow\Lib\Connect\Pdo\PdoAdapter;


class OrmConnection
{
    /**
     * @var PdoAdapter
     */
    protected $pdoAdapter;


    private function __construct()
    {
    }


    /**
     * @return static|Ret<static>
     */
    public static function from($from, ?array $fallback = null)
    {
        $ret = Ret::new();

        $instance = null
            ?? static::fromStatic($from)->orNull($ret)
            ?? static::fromPdoAdapter($from)->orNull($ret)
            ?? static::fromArray($from)->orNull($ret);

        if ($ret->isFail()) {
            return Ret::throw($fallback, $ret);
        }

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromStatic($from, ?array $fallback = null)
    {
        if ($from instanceof static) {
            return Ret::ok($fallback, $from);
        }

        return Ret::throw(
            $fallback,
            [ 'The `from` should be instance of: ' . static::class, $from ],
            [ __FILE__, __LINE__ ]
        );
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromPdoAdapter($from, ?array $fallback = null)
    {
        if (! ($from instanceof PdoAdapter)) {
            return Ret::throw(
                $fallback,
                [ 'The `from` should be instance of: ' . PdoAdapter::class, $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        $instance = new static();
        $instance->pdoAdapter = $from;

        return Ret::ok($fallback, $instance);
    }

    /**
     * @return static|Ret<static>
     */
    public static function fromArray($from, ?array $fallback = null)
    {
        if (! is_array($from)) {
            return Ret::throw(
                $fallback,
                [ 'The `from` should be array', $from ],
                [ __FILE__, __LINE__ ]
            );
        }

        if (! PdoAdapter::fromArray($from)->isOk([ &$pdoAdapter, &$ret ])) {
            return Ret::throw($fallback, $ret);
        }

        $instance = new static();
        $instance->pdoAdapter = $pdoAdapter;

        return Ret::ok($fallback, $instance);
    }


    public function getPdoAdapter() : PdoAdapter
    {
        return $this->pdoAdapter;
    }
}
