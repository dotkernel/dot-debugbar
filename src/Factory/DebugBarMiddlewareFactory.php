<?php

declare(strict_types=1);

namespace Dot\DebugBar\Factory;

use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;
use Exception;
use Mezzio\Middleware\ErrorResponseGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class DebugBarMiddlewareFactory
{
    public const MESSAGE_MISSING_RESPONSE_INTERFACE = 'Unable to find ResponseInterface in the container';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container): DebugBarMiddlewareInterface
    {
        if (! $container->has(DebugBarInterface::class)) {
            throw new Exception(DebugBarFactory::MESSAGE_MISSING_DEBUG_BAR);
        }

        if (! $container->has(ResponseInterface::class)) {
            throw new Exception(self::MESSAGE_MISSING_RESPONSE_INTERFACE);
        }

        return new DebugBarMiddleware(
            $container->get(DebugBarInterface::class),
            $container->get(ResponseInterface::class),
            $container->has(ErrorResponseGenerator::class)
                ? $container->get(ErrorResponseGenerator::class)
                : null
        );
    }
}
