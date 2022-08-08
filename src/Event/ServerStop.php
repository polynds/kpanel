<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace Polynds\KPanel\Event;

use Swoole\Coroutine\Http\Server;

class ServerStop
{
    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }
}
