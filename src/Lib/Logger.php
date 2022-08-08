<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Lib;

/**
 * @method emergency($message, array $context = [])
 * @method alert($message, array $context = [])
 * @method critical($message, array $context = [])
 * @method error($message, array $context = [])
 * @method warning($message, array $context = [])
 * @method notice($message, array $context = [])
 * @method info($message, array $context = [])
 * @method debug($message, array $context = [])
 * @method log($message, array $context = [])
 */
class Logger
{
    public function __call($name, $arguments)
    {
        var_dump(func_get_args());
    }
}
