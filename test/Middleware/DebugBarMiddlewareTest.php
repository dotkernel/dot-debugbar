<?php

declare(strict_types=1);

namespace DotTest\DebugBar\Middleware;

use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;
use DotTest\DebugBar\DummyResponse;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionException;
use ReflectionMethod;

class DebugBarMiddlewareTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCanCreateMiddleware(): void
    {
        $debugBar   = $this->createMock(DebugBarInterface::class);
        $middleware = new DebugBarMiddleware($debugBar, new DummyResponse());
        $this->assertInstanceOf(DebugBarMiddlewareInterface::class, $middleware);
    }

    /**
     * @throws Exception
     */
    public function testCanProcess(): void
    {
        $debugBar = $this->createMock(DebugBarInterface::class);
        $request  = $this->createMock(ServerRequestInterface::class);
        $handler  = $this->createMock(RequestHandlerInterface::class);

        $request->expects($this->once())->method('getServerParams')->willReturn([
            'REMOTE_ADDR' => '127.0.0.1',
        ]);

        $debugBar->expects($this->once())->method('shouldEnable')->withAnyParameters()->willReturn(true);

        $middleware = new DebugBarMiddleware($debugBar, new DummyResponse());
        $response   = $middleware->process($request, $handler);
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCreateErrorHandler(): void
    {
        $debugBar   = $this->createMock(DebugBarInterface::class);
        $middleware = new DebugBarMiddleware($debugBar, new DummyResponse());
        $reflection = new ReflectionMethod(DebugBarMiddleware::class, 'createErrorHandler');

        $callable = $reflection->invoke($middleware);
        $this->assertIsCallable($callable);
    }
}
