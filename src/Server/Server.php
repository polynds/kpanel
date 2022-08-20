<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server;

use KPanel\Assets\Panel;
use KPanel\Lib\Config\ConfigInterface;
use KPanel\Lib\Event\EventDispatcher;
use KPanel\Lib\Logger\LoggerInterface;
use KPanel\Server\Event\ServerStartEvent;
use KPanel\Server\Event\ServerStopEvent;
use KPanel\Server\Event\WebSocketClosedEvent;
use KPanel\Server\Event\WebSocketReceivedEvent;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Server as SwooleServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Timer;
use Swoole\WebSocket\CloseFrame;
use Swoole\WebSocket\Frame;

class Server
{
    protected LoggerInterface $logger;

    protected ConfigInterface $config;

    protected ServerConfig $serverConfig;

    protected SwooleServer $httpServer;

    protected EventDispatcher $eventDispatcher;

    public function __construct(ConfigInterface $config, EventDispatcher $eventDispatcher, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
        $this->serverConfig = new ServerConfig($this->config->get('server'));
    }

    public function start()
    {
        $this->initServer();
        Coroutine::create(function () {
            $this->eventDispatcher->dispatch(new ServerStartEvent($this->httpServer, $this->serverConfig));
            $this->httpServer->start();
            $this->eventDispatcher->dispatch(new ServerStopEvent($this->httpServer));
        });
    }

    protected function initServer()
    {
        $this->httpServer = new SwooleServer($this->serverConfig->getHost(), $this->serverConfig->getPort());
        $this->handleHttp();
        $this->handleWebsocket();
    }

    protected function handleHttp()
    {
        $this->httpServer->handle('/', function (Request $request, Response $response) {
            $response->end((new Panel())->display($this->serverConfig->getHost(), $this->serverConfig->getPort()));
        });
    }

    protected function handleWebsocket()
    {
        $this->httpServer->handle('/websocket', function (Request $request, Response $response) {
            $response->upgrade();
            $this->loopCallback($request, $response);
//            while (true) {
//                $frame = $response->recv();
//                if ($frame === '') {
//                    $response->close();
//                    break;
//                }
//                if ($frame === false) {
//                    echo 'errorCode: ' . swoole_last_error() . "\n";
//                    $response->close();
//                    break;
//                }
//                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
//                    $response->close();
//                    break;
//                }
//
//                if ($frame->opcode == WEBSOCKET_OPCODE_PING) {
//                    echo "Ping frame received: Code {$frame->opcode}\n";
//                    $pongFrame = new Frame();
//                    $pongFrame->opcode = WEBSOCKET_OPCODE_PONG;
//                    $response->push($frame->fd, $pongFrame);
//                    break;
//                }
//
////                $response->push("Hello {$frame->data}!");
////                $response->push("How are you, {$frame->data}?");
//
//                if ($frame->data == 'getData') {
//                    $monitor = new Monitor();
//                    $response->push($monitor->html());
//                }
//
////                $monitor = new Monitor();
////                Timer::tick(1000, function () use ($monitor, $response) {
////                    $response->push($monitor->html());
////                });
//            }
        });
    }

    protected function loopCallback(Request $request, Response $response)
    {
        $running = true;
        while ($running) {
            /** @var ?Frame $frame */
            $frame = $this->receive($request,$response);
            if (! $frame) {
                $running = false;
            }
        }
    }

    protected function receive(Request $request,Response $response)
    {
        $frame = $response->recv();
        if ($frame === '' || $frame === false || $frame->data == 'close' || get_class($frame) === CloseFrame::class) {
            $this->logger->debug('errorCode: ' . swoole_last_error());
            $this->eventDispatcher->dispatch(new WebSocketClosedEvent($response, $frame, swoole_last_error()));
            $response->close();
            return null;
        }

        if ($frame->opcode == WEBSOCKET_OPCODE_PING) {
            $this->logger->debug("Ping frame received: Code {$frame->opcode}");
            $this->pong($response, $frame);
            return null;
        }

        $this->eventDispatcher->dispatch(new WebSocketReceivedEvent($request,$response, $frame));

        return $frame;
    }

    protected function pong(Response $response, Frame $frame)
    {
        $pongFrame = new Frame();
        $pongFrame->opcode = WEBSOCKET_OPCODE_PONG;
        $response->push($frame->fd, $pongFrame);
    }
}
