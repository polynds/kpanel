<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel;

use Polynds\KPanel\Lib\Event\EventDispatcherFactory;
use Polynds\KPanel\Server\Server;

class Application
{
    public function __construct()
    {
    }

    public function run()
    {
        EventDispatcherFactory::collect();

        if (! extension_loaded('swoole')) {
            return;
        }
        $server = new Server();
        $server->start();
    }
}
