<?php

declare(strict_types=1);

namespace Dot\DebugBar\Middleware;

use Dot\DebugBar\DebugBarInterface;
use ErrorException;
use Laminas\Stratigility\Middleware\ErrorResponseGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function error_reporting;
use function restore_error_handler;
use function set_error_handler;

class DebugBarMiddleware implements DebugBarMiddlewareInterface, MiddlewareInterface
{
    /** @var callable $responseGenerator */
    private $responseGenerator;
    /** @var callable $responseFactory */
    private $responseFactory;
    private DebugBarInterface $debugBar;

    public function __construct(
        DebugBarInterface $debugBar,
        callable $responseFactory,
        ?callable $responseGenerator = null
    ) {
        $this->debugBar          = $debugBar;
        $this->responseFactory   = static fn(): ResponseInterface => $responseFactory();
        $this->responseGenerator = $responseGenerator ?: new ErrorResponseGenerator();
    }

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

    private function createErrorHandler(): callable
    {
        return function (int $errno, string $errstr, string $errfile, int $errline): void {
            if (! (error_reporting() & $errno)) {
                return;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }

    private function handleThrowable(Throwable $e, ServerRequestInterface $request): ResponseInterface
    {
        $generator = $this->responseGenerator;
        return $generator($e, $request, ($this->responseFactory)());
    }
}
