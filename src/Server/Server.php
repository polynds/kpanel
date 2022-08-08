<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace Polynds\KPanel\Server;

use Polynds\KPanel\ApplicationContext;
use Polynds\KPanel\Assets\Panel;
use Polynds\KPanel\Event\ServerStart;
use Polynds\KPanel\Event\ServerStop;
use Polynds\KPanel\Lib\Config;
use Polynds\KPanel\Lib\Event\EventDispatcher;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Server as SwooleServer;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Timer;
use Swoole\WebSocket\CloseFrame;
use Swoole\WebSocket\Frame;

class Server
{
    protected Config $config;

    protected ServerConfig $serverConfig;

    protected SwooleServer $swooleServer;

    protected EventDispatcher $eventDispatcher;

    public function __construct()
    {
        $this->config = ApplicationContext::getContainer()->get(Config::class);
        $this->serverConfig = new ServerConfig($this->config->get('server'));
        $this->eventDispatcher = ApplicationContext::getContainer()->get(EventDispatcher::class);
    }

    public function start()
    {
        $this->initServer();
        Coroutine::create(function () {
            $this->eventDispatcher->dispatch(new ServerStart($this->swooleServer, $this->serverConfig));
            $this->swooleServer->start();
            $this->eventDispatcher->dispatch(new ServerStop($this->swooleServer));
        });
    }

    protected function initServer()
    {
        $this->swooleServer = new SwooleServer($this->serverConfig->getHost(), $this->serverConfig->getPort());
        $this->handleHttp();
        $this->handleWebsocket();
    }

    protected function handleHttp()
    {
        $this->swooleServer->handle('/', function (Request $request, Response $response) {
            $response->end((new Panel())->display($this->serverConfig->getHost(), $this->serverConfig->getPort()));
        });
    }

    protected function handleWebsocket()
    {
        $this->swooleServer->handle('/websocket', function (Request $request, Response $ws) {
            $ws->upgrade();
            while (true) {
                $frame = $ws->recv();
                if ($frame === '') {
                    $ws->close();
                    break;
                }
                if ($frame === false) {
                    echo 'errorCode: ' . swoole_last_error() . "\n";
                    $ws->close();
                    break;
                }
                if ($frame->data == 'close' || get_class($frame) === CloseFrame::class) {
                    $ws->close();
                    break;
                }

                if ($frame->opcode == WEBSOCKET_OPCODE_PING) {
                    echo "Ping frame received: Code {$frame->opcode}\n";
                    $pongFrame = new Frame();
                    $pongFrame->opcode = WEBSOCKET_OPCODE_PONG;
                    $ws->push($frame->fd, $pongFrame);
                    break;
                }

//                $ws->push("Hello {$frame->data}!");
//                $ws->push("How are you, {$frame->data}?");

                if ($frame->data == 'getData') {
                    $monitor = new Monitor();
                    $ws->push($monitor->html());
                }

//                $monitor = new Monitor();
//                Timer::tick(1000, function () use ($monitor, $ws) {
//                    $ws->push($monitor->html());
//                });
            }
        });
    }
}
