<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel;

use KPanel\Lib\DI\ContainerInterface;

class ApplicationContext
{
    protected static ?ContainerInterface $container;

    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }

    public static function hasContainer(): bool
    {
        return ! is_null(self::$container);
    }

    public static function setContainer(ContainerInterface $container): ContainerInterface
    {
        self::$container = $container;
        return $container;
    }
}
