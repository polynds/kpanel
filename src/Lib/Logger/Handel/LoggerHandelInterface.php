<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Logger\Handel;

use KPanel\Lib\Logger\Handel\Formatter\FormatterInterface;

interface LoggerHandelInterface
{
    public function write(FormatterInterface $formatter);
}
