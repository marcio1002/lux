<?php

namespace Lux\Factory;

abstract class Factory {

    /**
     * Build the object in the factory
     *
     * @param mixed $params
     * @return object
     */
    abstract function create(...$params): object;
    
    /**
     * Undocumented function
     *
     * @param string $class
     * @param string $method
     * @return object
     */
    protected function handlerClass(string $class, string $method = ''): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        $className = $reflection->getName();

        $configProviders = require_once dirname(__DIR__, 2) . '/config/app.php';
        $configParams = $configProviders[$className] ?? [];

        if (!empty($constructor)) {
            $params = $this->getParams($class, '__construct', $configParams);
            return $reflection->newInstance(...$params);
        }

        return $reflection->newInstance();
    }


    /**
     * Get method parameters
     *
     * @param object|string $class
     * @param string $method
     * @return array
     */
    protected function getParams($class, string $method, $values = []): array
    {
        $reflectionMethod = new \ReflectionMethod($class, $method);
        $params = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $type = $parameter->getType();
            $name = $parameter->getName();
            $nameType = $type && ($type instanceof \ReflectionNamedType) ? $type->getName() : null;

            if (class_exists($nameType)) {
                $objectParams = isset($values[$name]) ? $values[$name] : null;

                $value = $objectParams ?
                    new $nameType(...is_array($objectParams) ?: [$objectParams]) :
                    new $nameType();
            }

            if ($parameter->isDefaultValueAvailable() && !isset($values[$name])) {
                $value = $parameter->getDefaultValue();
            }

            $params[] = $values[$name] ?? $value;
        }

        return $params;
    }
}