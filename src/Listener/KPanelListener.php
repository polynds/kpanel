<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Listener;

use Polynds\KPanel\ApplicationContext;
use Polynds\KPanel\Event\ServerStart;
use Polynds\KPanel\Event\ServerStop;
use Polynds\KPanel\Lib\Event\ListenerInterface;
use Polynds\KPanel\Lib\Logger;

class KPanelListener implements ListenerInterface
{
    protected Logger $logger;

    public function __construct()
    {
        $this->logger = ApplicationContext::getContainer()->get(Logger::class);
    }

    public function listen(): array
    {
        return [
            ServerStart::class,
            ServerStop::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof ServerStart) {
            $this->logger->info('KPanelServer started.');
        } elseif ($event instanceof ServerStop) {
            $this->logger->info('KPanelServer stoped.');
        }
    }
}
