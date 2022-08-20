<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel;

use KPanel\Lib\Config\Config;
use KPanel\Lib\Config\ConfigInterface;
use KPanel\Lib\Event\EventDispatcherFactory;
use KPanel\Lib\Logger\Logger;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Server\Server;

class Application
{
    protected EventDispatcherFactory $eventDispatcherFactory;

    protected Server $server;

    public function __construct()
    {
        $this->loadDependency();

        $this->eventDispatcherFactory = ApplicationContext::getContainer()->get(EventDispatcherFactory::class);
        $this->server = ApplicationContext::getContainer()->get(Server::class);
    }

    public function run()
    {
        if (! extension_loaded('swoole')) {
            return;
        }

        $this->eventDispatcherFactory->collect();

        $this->server->start();
    }

    protected function loadDependency()
    {
        ApplicationContext::getContainer()->set(ConfigInterface::class, ApplicationContext::getContainer()->get(Config::class));
        ApplicationContext::getContainer()->set(LoggerInterface::class, ApplicationContext::getContainer()->get(Logger::class));
    }
}
