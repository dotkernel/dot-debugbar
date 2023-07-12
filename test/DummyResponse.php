<?php

declare(strict_types=1);

namespace DotTest\DebugBar;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class DummyResponse implements ResponseInterface
{
    public function __invoke(): ResponseInterface
    {
        return new self();
    }

    public function getProtocolVersion(): string
    {
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
    }

    public function getHeaders(): array
    {
    }

    public function hasHeader(string $name): bool
    {
    }

    public function getHeader(string $name): array
    {
    }

    public function getHeaderLine(string $name): string
    {
    }

    public function withHeader(string $name, mixed $value): MessageInterface
    {
    }

    public function withAddedHeader(string $name, mixed $value): MessageInterface
    {
    }

    public function withoutHeader(string $name): MessageInterface
    {
    }

    public function getBody(): StreamInterface
    {
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
    }

    public function getStatusCode(): int
    {
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
    }

    public function getReasonPhrase(): string
    {
    }
}
