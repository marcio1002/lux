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
        [$class, $values] = $params;
        $method = $params[2] ?? null;

        $construct = (new \ReflectionClass($class))->getConstructor();

        if($construct)
            $params = $this->getParams($class, '__construct', $values);

        if($method)
            $params = $this->getParams($class, $method, $values);

        return new $class(...$params ?? []);
    }
}