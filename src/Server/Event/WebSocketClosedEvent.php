<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Event;

use Swoole\Http\Response;
use Swoole\WebSocket\Frame;

class WebSocketClosedEvent
{
    protected $errorCode;

    protected Frame $frame;

    protected Response $response;

    public function __construct(Response $response, Frame $frame, $errorCode)
    {
        $this->frame = $frame;
        $this->response = $response;
        $this->errorCode = $errorCode;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function getFrame(): Frame
    {
        return $this->frame;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
