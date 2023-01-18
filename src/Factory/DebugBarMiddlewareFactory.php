<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Factory;

use Dot\DebugBar\DebugBar;
use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class DebugBarMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @return DebugBarMiddlewareInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DebugBarMiddlewareInterface
    {
        return new DebugBarMiddleware(
            $container->get(DebugBar::class),
            $container->get(ResponseInterface::class),
            $container->has(ErrorResponseGenerator::class)
                ? $container->get(ErrorResponseGenerator::class)
                : null
        );
    }
}
