<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Lib\Event;

use Polynds\KPanel\Lib\Config;
use Polynds\KPanel\ApplicationContext;

class EventDispatcherFactory
{
    public static function collect()
    {
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = ApplicationContext::getContainer()->get(EventDispatcher::class);
        /** @var Config $config */
        $config = ApplicationContext::getContainer()->get(Config::class);
        $listeners = $config->get('listeners');
        foreach ($listeners as $listener) {
            $eventDispatcher->addListeners(new $listener);
        }
    }
}
