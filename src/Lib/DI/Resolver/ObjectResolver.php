<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\DI\Resolver;

use KPanel\Lib\DI\Container;
use ReflectionClass;
use ReflectionException;

class ObjectResolver implements DefinitionResolver
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolve(string $className)
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $prameters = [];
        try {
            $reflectionClass = new ReflectionClass($className);
            if ($reflectionClass->isInterface()) {
            }
            $constructor = $reflectionClass->getConstructor();
            foreach (($constructor ? $constructor->getParameters() : []) as $parameter) {
                if ($parameter->isOptional()) {
                    $prameters[] = $parameter;
                    continue;
                }
                $id = $parameter->getClass()->getName();
                $entry = $this->resolve($id);
                $this->container->set($id, $entry);
                $prameters[] = $entry;
            }
        } catch (ReflectionException $e) {
        }

        return new $className(...$prameters);
    }
}
