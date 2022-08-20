<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Server\Protocol;

use KPanel\Exception\ParamErrorException;
use KPanel\Utils\Json;

class JsonProtocol implements ProtocolInterface
{
    public const CMD_CALL = 'call';

    public const CMD_REPLY = 'reply';

    public const CMD_ERROR = 'error';

    protected string $cmd;

    protected string $action;

    protected array $data;

    protected string $message;

    public function __construct()
    {
    }

    public function encode()
    {
        return Json::encode(
            [
                'cmd' => $this->cmd,
                'action' => $this->action,
                'data' => $this->data,
                'message' => $this->message,
            ]
        );
    }

    public function decode($data): self
    {
        $data = Json::decode($data);
        if (! $data) {
            throw new ParamErrorException('Data decoding failed.');
        }

        $this->cmd = $data['cmd'] ?? '';
        $this->action = $data['action'] ?? '';
        $this->data = $data['data'] ? (is_array($data['data']) ?: [$data['data']]) : [];
        $this->message = $data['message'] ?? '';
        return $this;
    }

    public function getCmd(): string
    {
        return $this->cmd;
    }

    public function setCmd(string $cmd): void
    {
        $this->cmd = $cmd;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function error(string $message, array $data = [])
    {
        $this->cmd = self::CMD_ERROR;
        $this->action = '';
        $this->data = $data;
        $this->message = $message;
        return $this->encode();
    }

    public function reply(array $data, string $message = '')
    {
        $this->cmd = self::CMD_REPLY;
        $this->data = $data;
        $this->message = $message;
        return $this->encode();
    }
}
