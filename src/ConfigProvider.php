<?php

declare(strict_types=1);

namespace Dot\DebugBar;

use Dot\DebugBar\Extension\DebugBarExtension;
use Dot\DebugBar\Factory\DebugBarExtensionFactory;
use Dot\DebugBar\Factory\DebugBarFactory;
use Dot\DebugBar\Factory\DebugBarMiddlewareFactory;
use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;

class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    /**
     * @return array
     */
    public function getDependencyConfig(): array
    {
        return [
            'aliases'   => [
                DebugBarInterface::class           => DebugBar::class,
                DebugBarMiddlewareInterface::class => DebugBarMiddleware::class,
            ],
            'factories' => [
                DebugBar::class           => DebugBarFactory::class,
                DebugBarExtension::class  => DebugBarExtensionFactory::class,
                DebugBarMiddleware::class => DebugBarMiddlewareFactory::class,
            ],
        ];
    }
}
