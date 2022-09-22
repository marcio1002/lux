<?php

namespace Lux\Factory;

use Lux\Factory\Factory;

use Symfony\Component\Console\Command\Command;

class FactoryCommand extends Factory {

    /**
     * Undocumented function
     *
     * @param $command
     * @param $params
     * @return Command
     */
    public function create(...$params): Command
    {
        $class = array_shift($params);

        return $this->handlerClass(
            $class,
            '',
            ...$params
        );
    }
}