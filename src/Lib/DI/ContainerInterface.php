<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\DI;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    public function set(string $id, $entry): void;

    public function make(string $id, array $parameters = []);
}
