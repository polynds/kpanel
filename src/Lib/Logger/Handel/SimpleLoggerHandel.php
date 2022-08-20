<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Logger\Handel;

use KPanel\Lib\Logger\Handel\Formatter\FormatterInterface;

class SimpleLoggerHandel implements LoggerHandelInterface
{
    public function write(FormatterInterface $formatter)
    {
        var_dump($formatter->handel());
    }
}
