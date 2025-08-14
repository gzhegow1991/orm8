<?php

namespace Gzhegow\Orm;

use Illuminate\Container\Container as IlluminateContainer;
use Gzhegow\Orm\Core\Persistence\EloquentPersistenceInterface;
use Gzhegow\Orm\Package\Illuminate\Database\Capsule\EloquentInterface;
use Illuminate\Contracts\Events\Dispatcher as IlluminateEventDispatcher;


interface OrmBuilderInterface
{
    /**
     * @return static
     */
    public function setIlluminateContainer(?IlluminateContainer $container);

    /**
     * @return static
     */
    public function setIlluminateEventDispatcher(?IlluminateEventDispatcher $dispatcher);


    /**
     * @return static
     */
    public function setEloquent(?EloquentInterface $eloquent);

    /**
     * @return static
     */
    public function setPersistence(?EloquentPersistenceInterface $persistence);


    /**
     * @return array<string, OrmConnection>
     */
    public function getConnections() : array;

    /**
     * @param mixed|OrmConnection $connection
     *
     * @return static
     */
    public function addConnectionDefault($connection);

    /**
     * @param mixed|OrmConnection $connection
     *
     * @return static
     */
    public function addConnection(string $name, $connection);


    /**
     * @return static
     */
    public function defaultStringLength(?int $length);


    /**
     * @return static
     */
    public function fnInit(\Closure $fn);

    /**
     * @return static
     */
    public function fnBoot(\Closure $fn);

    /**
     * @return static
     */
    public function fnLog(\Closure $fn);


    public function make() : OrmInterface;
}
