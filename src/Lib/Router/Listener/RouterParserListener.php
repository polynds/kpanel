<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Router\Listener;

use KPanel\Lib\Event\ListenerInterface;
use KPanel\Lib\Router\Router;
use KPanel\Server\Event\ServerStartEvent;

class RouterParserListener implements ListenerInterface
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function listen(): array
    {
        return [
            ServerStartEvent::class,
        ];
    }

    public function process(object $event)
    {
        $this->router->parser();
    }
}
