<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Listener;

use Exception;
use KPanel\Lib\Event\ListenerInterface;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Lib\Router\Router;
use KPanel\Server\Event\WebSocketReceivedEvent;
use KPanel\Server\Monitor;
use KPanel\Server\Protocol\JsonProtocol;
use KPanel\Server\WebSocketContext;

class WebSocketReceivedListener implements ListenerInterface
{
    protected Router $router;

    protected LoggerInterface $logger;

    public function __construct(Router $router, LoggerInterface $logger)
    {
        $this->router = $router;
        $this->logger = $logger;
    }

    public function listen(): array
    {
        return [
            WebSocketReceivedEvent::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof WebSocketReceivedEvent) {
            if (! WebSocketContext::exist($event->getFrame()->fd)) {
                WebSocketContext::remember($event->getFrame()->fd, [
                    'request' => $event->getRequest(),
                    'frame' => $event->getFrame(),
                ]);
                $this->logger->info('fd:' . $event->getFrame()->fd . ' is Received.');
            }

            $protocol = new JsonProtocol();
            $frameData = $event->getFrame()->data ?? null;
            if (! $frameData) {
                $event->getResponse()->push($protocol->error('failed request.'));
            }

            try {
                $receive = $protocol->decode($frameData);
                if (empty($receive->getCmd()) || empty($receive->getAction())) {
                    $event->getResponse()->push($protocol->error('route does not exist！'));
                }

                $content = $this->router->handel($receive->getAction());
                $event->getResponse()->push($protocol->reply($content));
            } catch (Exception $e) {
                $event->getResponse()->push($protocol->error('Server Error: ' . $e->getMessage()));
            }

//            if ($event->getFrame()->data == 'getData') {
//                $monitor = new Monitor();
//                $event->getResponse()->push($monitor->html());
//            }
        }
    }
}
