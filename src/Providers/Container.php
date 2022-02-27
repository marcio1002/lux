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

    private function __construct()
    {
    }

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

        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';

        foreach ($appConfig['providers'] as $provider) {
            $providerInstance = $factoryProvider->create($provider, $this);

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
            $cms[] = $factoryCommand->create($className, $this->services);
        }

        $this->command->addCommands($cms);
        $this->command->run();
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

        $this->services[$className] = $instance;
    }

    public function bind(string $className, $callable)
    {
        $instance = $callable($this);

        $this->services[$className] = $instance;
    }

    public function singleton(string $className, $callable)
    {
        $instance = $callable($this);
        $hasInstance = isset($this->services) && in_array($className, $this->services);

        if (!$hasInstance) {
            $this->services[$className] = $instance;
        }
    }
}
