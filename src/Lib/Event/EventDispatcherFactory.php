<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Event;

use KPanel\Exception\ListenerNotExistException;
use KPanel\Lib\Config\ConfigInterface;
use KPanel\Lib\DI\ContainerInterface;

class EventDispatcherFactory
{
    protected EventDispatcher $eventDispatcher;

    protected ContainerInterface $container;

    protected ConfigInterface $config;

    public function __construct(EventDispatcher $eventDispatcher, ContainerInterface $container, ConfigInterface $config)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->container = $container;
        $this->config = $config;
    }

    public function collect()
    {
        $listeners = $this->config->get('listeners');
        foreach ($listeners as $listener) {
            if (! class_exists($listener)) {
                throw new ListenerNotExistException();
            }
            $this->eventDispatcher->addListeners($this->container->get($listener));
        }
    }
}
