<?php

declare(strict_types=1);

namespace DotTest\DebugBar\Factory;

use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Factory\DebugBarFactory;
use Dot\DebugBar\Factory\DebugBarMiddlewareFactory;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;
use DotTest\DebugBar\CommonTrait;
use DotTest\DebugBar\DummyResponse;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class DebugBarMiddlewareFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutDebugBar(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with(DebugBarInterface::class)
            ->willReturn(false);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_DEBUG_BAR);
        (new DebugBarMiddlewareFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateServiceWithoutResponseInterface(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            [DebugBarInterface::class, true],
            [ResponseInterface::class, false],
        ]);

        $this->expectExceptionMessage(DebugBarMiddlewareFactory::MESSAGE_MISSING_RESPONSE_INTERFACE);
        (new DebugBarMiddlewareFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateMiddleware(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $debugBar  = $this->createMock(DebugBarInterface::class);

        $container->method('has')->willReturnMap([
            [DebugBarInterface::class, true],
            [ResponseInterface::class, true],
        ]);

        $container->method('get')->willReturnMap([
            [DebugBarInterface::class, $debugBar],
            [ResponseInterface::class, new DummyResponse()],
        ]);

        $middleware = (new DebugBarMiddlewareFactory())($container);
        $this->assertInstanceOf(DebugBarMiddlewareInterface::class, $middleware);
    }
}
