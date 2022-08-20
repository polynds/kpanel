<?php

declare(strict_types=1);
/**
 * happy coding.
 */
namespace KPanel\Lib\Router;

use KPanel\Exception\ControllerNotFoundException;
use KPanel\Exception\RouterNotFoundException;
use KPanel\Lib\Config\ConfigInterface;
use KPanel\Lib\DI\ContainerInterface;

class Router
{
    protected ConfigInterface $config;

    protected ContainerInterface $container;

    protected array $routerMap = [];

    public function __construct(ConfigInterface $config, ContainerInterface $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    public function parser()
    {
        $routers = $this->config->get('router');
        foreach ($routers as $name => $class) {
            if (! class_exists($class)) {
                throw new ControllerNotFoundException();
            }

            $contorller = $this->container->get($class);
            if (! $contorller instanceof ControllerInterface) {
                throw new ControllerNotFoundException();
            }

            $this->routerMap[$name] = $contorller;
        }
    }

    public function handel(string $name)
    {
        if (! isset($this->routerMap[$name])) {
            throw new RouterNotFoundException(sprintf('%s Not Found.', $name));
        }
        /** @var ControllerInterface $handel */
        $handel = $this->routerMap[$name];
        return $handel->index();
    }
}
