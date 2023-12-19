<?php

namespace Lux\Providers;

use
    Symfony\Component\Console\Application,
    Lux\Factory\FactoryProvider,
    Lux\Factory\FactoryCommand,
    Symfony\Component\Finder\Finder;

class Container
{

    private static Container $instance;

    protected Application $command;

    /**
     * includes services containers
     *
     * @var array<array<object>>
     */
    private array $services = [];

    public static function getInstance(): Container
    {
        if (!isset(self::$instance)) {
            self::$instance = new Container();
        }

        return self::$instance;
    }

    public function run(Application $app): void
    {
        $this->command = $app;
        $factoryProvider = new FactoryProvider();
        $factoryCommand = new FactoryCommand();

        $factoryProvider->create($this);

        $class_commands = $factoryCommand->create($this->services);
        $this->command->addCommands($class_commands);
        $this->command->run();
    }

    /**
     * Resolve a container with the instance already created
     *
     * @param object $obj
     * @param callable|Closure $callable
     * @return void
     */
    public function instance(object $obj, $callable): void
    {
        $className = get_class($obj);
        $instance = $callable($obj, $this);

        $this->services[$className] = $instance;
    }

    /**
     * Resolve a container with the class name
     *
     * @param string $className 
     * @param callable|Closure $callable
     * @return void 
     */
    public function bind(string $className, $callable): void
    {
        $instance = $callable($this);

        $this->services[$className] = $instance;
    }

    /**
     * Resolve a container with class name as singleton
     *
     * @param string $className 
     * @param callable|Closure $callable
     * @return void 
     */
    public function singleton(string $className, $callable): void
    {
        $instance = $callable($this);
        $doesNotHaveInstance = !array_key_exists($className, $this->services);

        if ($doesNotHaveInstance) {
            $this->services[$className] = $instance;
        }
    }
}
