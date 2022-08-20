<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\DI;

use KPanel\Lib\DI\Resolver\DefinitionResolver;
use KPanel\Lib\DI\Resolver\ObjectResolver;
use Psr\Container\ContainerInterface as PsrContainerInterface;

class Container implements ContainerInterface
{
    protected array $resolvedEntries = [];

    protected DefinitionResolver $resolver;

    public function __construct()
    {
        $this->resolvedEntries = [
            self::class => $this,
            ContainerInterface::class => $this,
            PsrContainerInterface::class => $this,
        ];
        $this->resolver = new ObjectResolver($this);
    }

    public function make(string $id, array $parameters = [])
    {
        return new $id(...$parameters);
    }

    public function set(string $id, $entry): void
    {
        $this->resolvedEntries[$id] = $entry;
    }

    public function get(string $id)
    {
        if (isset($this->resolvedEntries[$id])) {
            return $this->resolvedEntries[$id];
        }
        return $this->resolvedEntries[$id] = $this->resolver->resolve($id);
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->resolvedEntries);
    }
}
