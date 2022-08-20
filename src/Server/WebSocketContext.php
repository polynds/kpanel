<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server;

use KPanel\Context;

class WebSocketContext
{
    public static function remember(int $fd, $data): bool
    {
        return Context::set(self::key($fd), $data);
    }

    public static function exist(int $fd): bool
    {
        return Context::has(self::key($fd));
    }

    public static function closed(int $fd): bool
    {
        return Context::remove(self::key($fd));
    }

    protected static function key(int $fd): string
    {
        return sprintf('KPanel_WS_FD_%d', $fd);
    }
}
