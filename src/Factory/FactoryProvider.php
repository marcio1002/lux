<?php

namespace Lux\Factory;

use
    Lux\Factory\Factory,
    Lux\Providers\Provider;

class FactoryProvider extends Factory
{

    public function create(...$params): Provider
    {      
        $className = $params[0];
        return $this->handlerClass($className);
    }
}
