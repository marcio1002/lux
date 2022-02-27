<?php

namespace Lux\Providers;

/**
 * Class Provider
 * @method mixed register()
 * 
 * @see \Lux\Providers\Provider
 */
abstract class Provider
{
    protected Container $app;

    public function __construct(Container $container)
    {
        $this->app = $container;
    }

    /**
     * Register the service container
     *
     * @return mixed
     */
    abstract function boot();
}
