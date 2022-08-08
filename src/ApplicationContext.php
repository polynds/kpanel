<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel;

use Polynds\KPanel\Lib\DI\Container;

class ApplicationContext
{
    protected static ?Container $container;

    public static function getContainer(): Container
    {
        return self::$container;
    }

    public static function hasContainer(): bool
    {
        return ! is_null(self::$container);
    }

    public static function setContainer(Container $container): Container
    {
        self::$container = $container;
        return $container;
    }
}
