<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Logger;

interface LoggerInterface
{
    public const LEVEL_EMERGENCY = 'emergency';

    public const LEVEL_ALERT = 'alert';

    public const LEVEL_CRITICAL = 'critical';

    public const LEVEL_ERROR = 'error';

    public const LEVEL_WARNING = 'warning';

    public const LEVEL_NOTICE = 'notice';

    public const LEVEL_INFO = 'info';

    public const LEVEL_DEBUG = 'debug';

    public const LEVEL_LOG = 'log';

    public function emergency($message, array $context = []);

    public function alert($message, array $context = []);

    public function critical($message, array $context = []);

    public function error($message, array $context = []);

    public function warning($message, array $context = []);

    public function notice($message, array $context = []);

    public function info($message, array $context = []);

    public function debug($message, array $context = []);

    public function log($message, array $context = []);
}
