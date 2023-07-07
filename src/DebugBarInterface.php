<?php

declare(strict_types=1);

namespace Dot\DebugBar;

use Closure;
use DebugBar\DebugBarException;
use Throwable;

interface DebugBarInterface
{
    public function addMessage(mixed $message, string $label = 'info', bool $isString = true): self;

    public function addThrowable(Throwable $throwable): self;

    public function disable(): self;

    public function enable(): self;

    public function getJavascriptRenderer(?string $baseUrl = null, ?string $basePath = null);

    public function isEnabled(): bool;

    public function measure(string $name, Closure $closure): self;

    public function shouldEnable(string $ipAddress): bool;

    public function startTimer(string $name, ?string $label = null): self;

    /**
     * @throws DebugBarException
     */
    public function stopTimer(string $name, array $params = []): self;
}
