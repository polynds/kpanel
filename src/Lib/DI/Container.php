<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Lib\DI;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected array $instance;

    public static function __callStatic($method, $arguments)
    {
        return (new static())->{$method}(...$arguments);
    }

    public function make(string $name, array $parameters = [])
    {
        $this->instance[$name] = (new $name(...$parameters));
    }

    public function set(string $name, $entry): void
    {
        $this->instance[$name] = $entry;
    }

    public function get(string $id)
    {
        return $this->instance[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->instance[$id]);
    }
}
