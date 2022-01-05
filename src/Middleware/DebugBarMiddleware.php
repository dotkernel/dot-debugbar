<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Middleware;

use Dot\DebugBar\DebugBar;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DebugBarMiddleware implements DebugBarMiddlewareInterface, MiddlewareInterface
{
    private DebugBar $debugBar;

    /**
     * @param DebugBar $debugBar
     */
    public function __construct(DebugBar $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->debugBar->shouldEnable($request->getServerParams()['REMOTE_ADDR'])) {
            $this->debugBar->enable();
        }

        return $handler->handle($request);
    }
}
