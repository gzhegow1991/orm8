<?php

namespace Gzhegow\Orm\Core;

use Gzhegow\Orm\Core\Orm\OrmConnection;
use Gzhegow\Lib\Connect\Pdo\PdoAdapter;
use Gzhegow\Orm\Exception\RuntimeException;
use Gzhegow\Lib\Exception\Runtime\ComposerException;
use Gzhegow\Orm\Core\Persistence\EloquentPersistence;
use Illuminate\Container\Container as IlluminateContainer;
use Gzhegow\Orm\Package\Illuminate\Database\Capsule\Eloquent;
use Gzhegow\Orm\Core\Persistence\EloquentPersistenceInterface;
use Illuminate\Database\Schema\Builder as IlluminateSchemaBuilder;
use Gzhegow\Orm\Package\Illuminate\Database\Capsule\EloquentInterface;
use Illuminate\Contracts\Events\Dispatcher as IlluminateEventDispatcher;


class OrmBuilder implements OrmBuilderInterface
{
    /**
     * @var OrmFactoryInterface
     */
    protected $factory;

    /**
     * @var IlluminateContainer
     */
    protected $illuminateContainer;
    /**
     * @var IlluminateEventDispatcher
     */
    protected $illuminateEventDispatcher;

    /**
     * @var EloquentInterface
     */
    protected $eloquent;
    /**
     * @var EloquentPersistenceInterface
     */
    protected $persistence;

    /**
     * @var array<string, OrmConnection>
     */
    protected $connectionList = [];

    /**
     * @var int
     */
    protected $defaultStringLength;

    /**
     * @var \Closure
     */
    protected $fnLog;

    /**
     * @var \Closure
     */
    protected $fnInit;
    /**
     * @var \Closure
     */
    protected $fnBoot;


    public function __construct(OrmFactoryInterface $factory)
    {
        $this->factory = $factory;
    }


    /**
     * @return static
     */
    public function setIlluminateContainer(?IlluminateContainer $container)
    {
        $this->illuminateContainer = $container;

        return $this;
    }

    /**
     * @return static
     */
    public function setIlluminateEventDispatcher(?IlluminateEventDispatcher $dispatcher)
    {
        $this->illuminateEventDispatcher = $dispatcher;

        return $this;
    }


    /**
     * @return static
     */
    public function setEloquent(?EloquentInterface $eloquent)
    {
        $this->eloquent = $eloquent;

        return $this;
    }

    /**
     * @return static
     */
    public function setPersistence(?EloquentPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;

        return $this;
    }


    /**
     * @return array<string, OrmConnection>
     */
    public function getConnections() : array
    {
        return $this->connectionList;
    }

    /**
     * @param mixed|OrmConnection $connection
     *
     * @return static
     */
    public function addConnectionDefault($connection)
    {
        return $this->addConnection('default', $connection);
    }

    /**
     * @param mixed|OrmConnection $connection
     *
     * @return static
     */
    public function addConnection(string $name, $connection)
    {
        if (isset($this->connectionList[ $name ])) {
            throw new RuntimeException(
                [ 'The connection with given name is already registered: ' . $name, $connection ]
            );
        }

        $connectionObject = OrmConnection::from($connection)->orThrow();

        $this->connectionList[ $name ] = $connectionObject;

        return $this;
    }


    /**
     * @return static
     */
    public function defaultStringLength(?int $length)
    {
        $this->defaultStringLength = $length;

        return $this;
    }


    /**
     * @return static
     */
    public function fnInit(\Closure $fn)
    {
        $this->fnInit = $fn;

        return $this;
    }

    /**
     * @return static
     */
    public function fnBoot(\Closure $fn)
    {
        $this->fnBoot = $fn;

        return $this;
    }

    /**
     * @return static
     */
    public function fnLog(\Closure $fn)
    {
        $this->fnLog = $fn;

        return $this;
    }


    public function make() : OrmInterface
    {
        $hasIlluminateEventDispatcher = (null !== $this->illuminateEventDispatcher);
        $hasFnLog = (null !== $this->fnLog);

        if (null === $this->eloquent) {
            $illuminateContainer = $this->illuminateContainer ?? null;

            $this->eloquent = new Eloquent($illuminateContainer);
        }

        if (null === $this->persistence) {
            $this->persistence = new EloquentPersistence($this->eloquent);
        }

        if (false
            || $hasIlluminateEventDispatcher
            || $hasFnLog
        ) {
            if (! $hasIlluminateEventDispatcher) {
                $commands = [
                    'composer require illuminate/events',
                ];

                if (! class_exists($eventsDispatcherClass = '\Illuminate\Events\Dispatcher')) {
                    throw new ComposerException([
                        ''
                        . 'Please, run following commands: '
                        . '[ ' . implode(' ][ ', $commands) . ' ]',
                    ]);
                }

                $this->illuminateEventDispatcher = new $eventsDispatcherClass();
            }

            $this->eloquent->setEventDispatcher($this->illuminateEventDispatcher);
        }

        if ([] !== $this->connectionList) {
            foreach ( $this->connectionList as $name => $ormConnection ) {
                $pdoAdapter = $ormConnection->getPdoAdapter();

                $pdoDefaultConfig = $pdoAdapter->getConfigDefault();

                if (null === $pdoDefaultConfig[ 'pdo' ]) {
                    $eloquentConnectionConfig = $this->convertPdoAdapterToEloquentConfig($pdoAdapter);

                    $this->eloquent->addConnection($eloquentConnectionConfig, $name);

                } else {
                    $eloquentConnectionConfig = [
                        'driver'   => $pdoDefaultConfig[ 'driver' ],
                        'database' => '',
                    ];

                    $this->eloquent->addConnection($eloquentConnectionConfig, $name);

                    $conn = $this->eloquent->getConnection($name);

                    $conn->setPdo(
                        function () use ($pdoAdapter) {
                            return $pdoAdapter->getPdoDefault();
                        }
                    );
                    $conn->setReadPdo(
                        function () use ($pdoAdapter) {
                            return $pdoAdapter->getPdoRead();
                        }
                    );
                }
            }
        }

        if (null !== $this->fnInit) {
            call_user_func_array($this->fnInit, [ $this->eloquent ]);
        }

        if (null !== $this->defaultStringLength) {
            IlluminateSchemaBuilder::$defaultStringLength = $this->defaultStringLength;
        }

        $this->eloquent->bootEloquent();

        if (null !== $this->fnBoot) {
            call_user_func_array($this->fnBoot, [ $this->eloquent ]);
        }

        if ($hasFnLog) {
            $connection = $this->eloquent->getConnection();
            $connection->enableQueryLog();
            $connection->listen($this->fnLog);
        }

        $facade = new OrmFacade(
            $this->factory,
            $this->eloquent,
            $this->persistence
        );

        return $facade;
    }


    protected function convertPdoAdapterToEloquentConfig(PdoAdapter $pdoAdapter) : array
    {
        $configDefault = $pdoAdapter->getConfigDefault();
        $configsRead = $pdoAdapter->getReadConfigs();
        $configsWrite = $pdoAdapter->getWriteConfigs();

        $eloquentConfig = $this->convertPdoAdapterConfigToEloquentConfig($configDefault);

        if ([] !== $configsRead) {
            foreach ( $configsRead as $c ) {
                $eloquentConfig[ 'read' ][] = $this->convertPdoAdapterConfigToEloquentConfig($c);
            }
        }

        if ([] !== $configsWrite) {
            foreach ( $configsWrite as $c ) {
                $eloquentConfig[ 'write' ][] = $this->convertPdoAdapterConfigToEloquentConfig($c);
            }
        }

        return $eloquentConfig;
    }

    protected function convertPdoAdapterConfigToEloquentConfig(array $pdoAdapterConfig) : array
    {
        $pdo = $pdoAdapterConfig[ 'pdo' ];
        $host = $pdoAdapterConfig[ 'host' ];
        $port = $pdoAdapterConfig[ 'port' ];
        $sock = $pdoAdapterConfig[ 'sock' ];

        $isPdo = (null !== $pdo);
        $isHost = (null !== $host);
        $isSock = (null !== $sock);

        if ($isPdo) {
            throw new RuntimeException(
                [
                    'The `pdo-like` configs should not be converted using this function',
                    $pdoAdapterConfig,
                ]
            );
        }

        $eloquentConfig = [];
        $eloquentConfig[ 'driver' ] = $pdoAdapterConfig[ 'driver' ];
        $eloquentConfig[ 'username' ] = $pdoAdapterConfig[ 'username' ];
        $eloquentConfig[ 'password' ] = $pdoAdapterConfig[ 'password' ];
        $eloquentConfig[ 'database' ] = $pdoAdapterConfig[ 'database' ];
        $eloquentConfig[ 'charset' ] = $pdoAdapterConfig[ 'charset' ];
        $eloquentConfig[ 'collation' ] = $pdoAdapterConfig[ 'collate' ];
        $eloquentConfig[ 'options' ] = []
            + $pdoAdapterConfig[ 'pdo_options_boot' ]
            + $pdoAdapterConfig[ 'pdo_options_new' ];

        if ($isHost) {
            $eloquentConfig[ 'host' ] = $host;
            $eloquentConfig[ 'port' ] = $port;

        } elseif ($isSock) {
            $eloquentConfig[ 'host' ] = $sock;

        } else {
            throw new RuntimeException(
                [ 'Unable to convert config', $pdoAdapterConfig ]
            );
        }

        return $eloquentConfig;
    }
}
