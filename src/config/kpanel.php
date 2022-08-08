<?php

declare(strict_types=1);
/**
 * happy coding.
 */
use Polynds\KPanel\Listener\KPanelListener;

return [
    'enable' => true,
    'server' => [
        'name' => 'kpanel_server',
        'host' => '127.0.0.1',
        'port' => 9028,
    ],
    'listeners' => [
        KPanelListener::class,
    ],
];
