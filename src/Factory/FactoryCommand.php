<?php

namespace Lux\Factory;

use Lux\Factory\Factory;

use
    Symfony\Component\Finder\Finder,
    Symfony\Component\Console\Command\Command;

class FactoryCommand extends Factory
{

    /**
     * Load command classes
     * 
     * @param array<mixed> $params
     * @return Array<Command>
     */
    public function create(...$params): array
    {
        $cms = [];

        $commands = (new Finder)
            ->in(dirname(__DIR__, 1) . '/Commands')
            ->files()
            ->name('*Command.php');

        foreach ($commands as $command) {
            $className = 'Lux\\Commands\\' . $command->getBasename('.php');

            $cms[] = $this->handlerClass(
                class: $className,
                method: '',
                paramsValues: $params
            );
        }

        return $cms;
    }
}
