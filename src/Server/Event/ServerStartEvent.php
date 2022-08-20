<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Event;

use KPanel\Server\ServerConfig;
use Swoole\Coroutine\Http\Server;

class ServerStartEvent
{
    protected Server $server;

    protected ServerConfig $serverConfig;

    public function __construct(Server $server, ServerConfig $serverConfig)
    {
        $this->server = $server;
        $this->serverConfig = $serverConfig;
    }

    public function getServer(): Server
    {
        return $this->server;
    }

    public function getServerConfig(): ServerConfig
    {
        return $this->serverConfig;
    }
}
