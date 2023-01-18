<?php

declare(strict_types = 1);

namespace Dot\DebugBar\Middleware;

use Dot\DebugBar\DebugBar;
use ErrorException;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class DebugBarMiddleware implements DebugBarMiddlewareInterface, MiddlewareInterface
{
    /** @var callable Routine that will generate the error response. */
    private $responseGenerator;

    /** @var callable */
    private $responseFactory;

    /** @var DebugBar $debugBar */
    private DebugBar $debugBar;

    /**
     * @param DebugBar $debugBar
     * @param callable $responseFactory
     * @param callable|null $responseGenerator
     */
    public function __construct(DebugBar $debugBar, callable $responseFactory, ?callable $responseGenerator = null)
    {
        $this->debugBar = $debugBar;
        $this->responseFactory = static fn(): ResponseInterface => $responseFactory();
        $this->responseGenerator = $responseGenerator ?: new ErrorResponseGenerator();
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

            set_error_handler($this->createErrorHandler());

            try {
                $response = $handler->handle($request);
            } catch (Throwable $exception) {
                $this->debugBar->addThrowable($exception);
                $response = $this->handleThrowable($exception, $request);
            }

            restore_error_handler();

            return $response;
        } else {
            return $handler->handle($request);
        }
    }

    /**
     * Creates and returns a callable error handler that raises exceptions.
     *
     * Only raises exceptions for errors that are within the error_reporting mask.
     */
    private function createErrorHandler() : callable
    {
        return function (int $errno, string $errstr, string $errfile, int $errline) : void {
            if (! (error_reporting() & $errno)) {
                // error_reporting does not include this error
                return;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }

    /**
     * Handles all throwables, generating and returning a response.
     *
     * Passes the error, request, and response prototype to createErrorResponse(),
     * triggers all listeners with the same arguments (but using the response
     * returned from createErrorResponse()), and then returns the response.
     */
    private function handleThrowable(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $generator = $this->responseGenerator;
        return $generator($e, $request, ($this->responseFactory)());
    }
}
