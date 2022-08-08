<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Lib\Event;

class EventDispatcher
{
    /**
     * @var ListenerInterface[]
     */
    protected array $listeners;

    public function addListeners(ListenerInterface $listener): void
    {
        $this->listeners[] = $listener;
    }

    public function dispatch(object $event)
    {
        foreach ($this->listeners as $listener) {
            if (in_array(get_class($event), $listener->listen())) {
                $listener->process($event);
            }
        }
    }
}
