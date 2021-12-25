<?php

namespace Lux\Providers;


use
    Symfony\Component\Console\Application,
    Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Logger\ConsoleLogger;

abstract class Provider
{
    protected Finder $commands;

    protected Finder $providers;

    protected array $globalFunctions = [];

    protected function __init(): void
    {
        try {
            $this->commands = (new Finder())
                ->in(dirname(__DIR__, 1) . '/Commands')
                ->files()
                ->name('*Command.php');

            $this->providers = (new Finder())
                ->in(__DIR__)
                ->files()
                ->notName(['AppProvider.php', 'Provider.php'])
                ->name('*.php');
        } catch (\Throwable $ex) {
            file_put_contents('php://output', "{$ex->getMessage()}\n Line {$ex->getLine()} from {$ex->getFile()}");
            exit;
        }
    }

    protected function initProviders(): void
    {
        foreach ($this->providers as $file) {
            $reflection = new \ReflectionClass("Lux\\Providers\\{$file->getBasename('.php')}");

            if ($reflection->hasMethod('boot')) {
                $parametersConstructor = $reflection->getConstructor() ?: [];
                $parameters = $reflection->getMethod('boot')->getParameters();

                if ($parametersConstructor)
                    $parametersConstructor = $this->getParameters($parametersConstructor);
                if ($parameters)
                    $parameters = $this->getParameters($parameters);

                $reflection->getMethod('boot')->invokeArgs($reflection->newInstance(...$parametersConstructor), [$this]);
            }

            if ($reflection->hasMethod('register')) {
                $parametersConstructor = $reflection->getConstructor() ?: [];
                $parameters = $reflection->getMethod('register')->getParameters();
                if ($parameters) {
                    $reflection->getMethod('register')->invokeArgs($reflection->newInstance(...$parametersConstructor), [$this, ...$parameters]);
                } else
                    $reflection->getMethod('register')->invoke($reflection->newInstance(...$parametersConstructor));
            }
        }
    }

    public function initCommands(Application $app): void
    {
        $commands = [];
        foreach ($this->commands as $file) {
            $command = "Lux\\Commands\\{$file->getBasename('.php')}";
            $commands[] = new $command;
        }

        $app->addCommands($commands);
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * @param callable\Closure $func
     * @return void
     */
    public function bind(string $name, $function)
    {
        $function = !($function instanceof \Closure) ? \Closure::fromCallable($function) : $function;

        $this->globalFunctions[$name] = \Closure::bind($function, $this);
    }

    /**
     * get parameters 
     *
     * @param \ReflectionParameter[] $parameters
     * @return void
     */
    private function getParameters($parameters)
    {
        return $parameters;
    }
}
