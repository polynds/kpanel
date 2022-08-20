<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Event;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;

class WebSocketReceivedEvent
{
    protected Request $request;

    protected Response $response;

    protected Frame $frame;

    public function __construct(Request $request, Response $response, Frame $frame)
    {
        $this->frame = $frame;
        $this->response = $response;
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }
}
