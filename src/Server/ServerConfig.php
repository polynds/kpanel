<?php

declare(strict_types=1);
/**
 * happy coding!!!
 */
namespace KPanel\Server;

class ServerConfig
{
    protected string $host;

    protected int $port;

    protected array $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        isset($config['host']) && $this->setHost($config['host']);
        isset($config['port']) && $this->setPort($config['port']);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }
}
