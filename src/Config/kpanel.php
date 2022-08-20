<?php

declare(strict_types=1);
/**
 * happy coding.
 */
use KPanel\Controller\CpuController;
use KPanel\Controller\MemController;
use KPanel\Controller\NetController;
use KPanel\Lib\Config\Config;
use KPanel\Lib\Config\ConfigInterface;
use KPanel\Lib\Logger\Logger;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Lib\Router\Listener\RouterParserListener;
use KPanel\Server\Listener\ServerListener;
use KPanel\Server\Listener\WebSocketClosedListener;
use KPanel\Server\Listener\WebSocketReceivedListener;

return [
    'enable' => true,
    'server' => [
        'name' => 'kpanel_server',
        'host' => '127.0.0.1',
        'port' => 9028,
    ],
    'dependency' => [
        ConfigInterface::class => Config::class,
        LoggerInterface::class => Logger::class,
    ],
    'listeners' => [
        ServerListener::class,
        WebSocketReceivedListener::class,
        WebSocketClosedListener::class,
        RouterParserListener::class,
    ],
    'router' => [
        'stat/cpu' => CpuController::class,
        'stat/mem' => MemController::class,
        'stat/net' => NetController::class,
    ],
];
