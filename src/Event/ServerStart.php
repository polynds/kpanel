<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace Polynds\KPanel\Event;

use Polynds\KPanel\Server\ServerConfig;
use Swoole\Coroutine\Http\Server;

class ServerStart
{
    public Server $server;

    public ServerConfig $serverConfig;

    public function __construct(Server $server, ServerConfig $serverConfig)
    {
        $this->server = $server;
        $this->serverConfig = $serverConfig;
    }
}
