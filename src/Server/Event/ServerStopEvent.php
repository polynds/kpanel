<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Event;

use Swoole\Coroutine\Http\Server;

class ServerStopEvent
{
    protected Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getServer(): Server
    {
        return $this->server;
    }
}
