<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Listener;

use KPanel\Lib\Event\ListenerInterface;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Server\Event\ServerStartEvent;
use KPanel\Server\Event\ServerStopEvent;

class ServerListener implements ListenerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function listen(): array
    {
        return [
            ServerStartEvent::class,
            ServerStopEvent::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof ServerStartEvent) {
            $this->logger->info('KPanelServer started.');
        } elseif ($event instanceof ServerStopEvent) {
            $this->logger->info('KPanelServer stoped.');
        }
    }
}
