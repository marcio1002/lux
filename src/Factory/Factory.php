<?php

namespace Lux\Factory;

abstract class Factory
{

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
     * @param object|string $class
     * @param string $method
     * @return object
     */
    protected function handlerClass($class, string $method = '', array $paramsValues = [], $static = false): object
    {
        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();
        $className = $reflection->getName();

        if (!$reflection->isInstantiable()) {
            throw new \Exception("The class {$className} is not instantiable");
        }

        if (!empty($constructor) && empty($method)) {
            $params = $this->getParams($class, '__construct', $paramsValues);
            return $reflection->newInstance(...$params);
        }

        if (!empty($method)) {
            $params = $this->getParams($class, $method, $className);
            return $reflection->newInstanceWithoutConstructor()->$method(...$params);
        }

        if ($static && !empty($method)) {
            $params = $this->getParams($class, $method, $className);

            return !empty($method) ? $class::$method(...$params) : $class;
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
            $reflectionType = $parameter->getType();
            $name = $parameter->getName();
            $type = $reflectionType && ($reflectionType instanceof \ReflectionNamedType) ? $reflectionType->getName() : null;

            if (
                $parameter->isDefaultValueAvailable() &&
                !in_array($name, $values) &&
                !in_array($type, array_keys($values))
            ) {
                $value = $parameter->getDefaultValue();
            }

            if (
                class_exists($type) &&
                $reflectionMethod->getDeclaringClass()->isInstantiable() &&
                !in_array($type, array_keys($values))
            ) {
                $appConfig = require dirname(__DIR__, 2) . '/config/app.php';

                $value = $this->handlerClass(
                    $type,
                    '',
                    $appConfig[$reflectionMethod->getDeclaringClass()->getName()] ?? []
                );
            }

            if (in_array($type, array_keys($values))) {
                $value = $values[$type];
            }

            $params[] = $values[$name] ?? $value;
        }

        return $params;
    }
}
