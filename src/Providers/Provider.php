<?php

namespace Lux\Providers;

use
    Symfony\Component\Console\Application,
    Lux\Factory\FactoryProvider,
    Lux\Factory\FactoryCommand,
    Symfony\Component\Finder\Finder;

/**
 * Class Provider
 * @method mixed register()
 * 
 * @see \Lux\Providers\Provider
 */
abstract class Provider
{

    /**
     * @method mixed register()
     */

    protected Application $app;

    /**
     * includes services containers
     *
     * @var array<array<object>>
     */
    private array $containers = [];

    /**
     * Register the service container
     *
     * @return mixed
     */
    abstract function boot();

    public function run(Application $app): void
    {
        $this->app = $app;

        $factoryProvider = new FactoryProvider();
        $factoryCommand = new FactoryCommand();

        $providers = require_once dirname(__DIR__, 2) . '/config/app.php';

        foreach ($providers['providers'] as $provider) {
            $providerInstance = $factoryProvider->create($provider);

            if (method_exists($providerInstance, 'register')) {
                $providerInstance->register();
            }

            $providerInstance->boot();
        }

        $commands = (new Finder)
            ->in(dirname(__DIR__, 1) . '/Commands')
            ->files()
            ->name('*Command.php');


        $cms = [];
        foreach ($commands as $command) {
            $className = 'Lux\Commands\\' . $command->getBasename('.php');
            $cms[] = $factoryCommand->create($className, $this->containers);
        }

        $this->app->addCommands($cms);
        $this->app->run();
    }

    /**
     * Resolve a container with the instance already created
     *
     * @param object $obj
     * @param callable|Closure $callable
     * @return void
     */
    public function instance(object $obj, $callable)
    {
        $className = get_class($obj);
        $instance = $callable($obj, $this);
        $hasInstance = isset($this->containers) && in_array($className, $this->containers);

        if (!$hasInstance) {
            $this->containers[$className] = $instance;
        }
    }

    public function bind(string $className, $callable)
    {
        $instance = $callable($this);
        $hasInstance = isset($this->containers) && in_array($className, $this->containers);

        if (!$hasInstance) {
            $this->containers[$className] = $instance;
        }
    }
}
