<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Listener;

use KPanel\Lib\Event\ListenerInterface;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Server\Event\WebSocketClosedEvent;
use KPanel\Server\WebSocketContext;

class WebSocketClosedListener implements ListenerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function listen(): array
    {
        return [
            WebSocketClosedEvent::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof WebSocketClosedEvent) {
            WebSocketContext::closed($event->getFrame()->fd);
            $this->logger->info('fd:' . $event->getFrame()->fd . ' is closed and errorCode:' . $event->getErrorCode());
        }
    }
}
