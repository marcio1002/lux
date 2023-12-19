<?php

namespace Lux\Factory;

use Lux\Factory\Factory;

class FactoryProvider extends Factory
{
    /**
     * Load classes providers
     * 
     * @param array<mixed> ...$params 
     */
    public function create(...$params): mixed
    {      
        $appConfig = require_once dirname(__DIR__, 1) . '/config/app.php';
        $container = $params[0];

        foreach ($appConfig['providers'] as $provider) {
            $providerInstance = $this->handlerClass(
                class: $provider,
                method: '',
                paramsValues: compact('container')
            );

            if (method_exists($providerInstance, 'register')) {
                $providerInstance->register();
            }

            $providerInstance->boot();
        }

        return null;
    }
}
