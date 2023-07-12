<?php

declare(strict_types=1);

namespace DotTest\DebugBar;

use Dot\DebugBar\ConfigProvider;
use Dot\DebugBar\DebugBar;
use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Extension\DebugBarExtension;
use Dot\DebugBar\Factory\DebugBarExtensionFactory;
use Dot\DebugBar\Factory\DebugBarFactory;
use Dot\DebugBar\Factory\DebugBarMiddlewareFactory;
use Dot\DebugBar\Middleware\DebugBarMiddleware;
use Dot\DebugBar\Middleware\DebugBarMiddlewareInterface;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    protected array $config;

    protected function setup(): void
    {
        $this->config = (new ConfigProvider())();
    }

    public function testHasDependencies(): void
    {
        $this->assertArrayHasKey('dependencies', $this->config);
    }

    public function testDependenciesHasFactories(): void
    {
        $this->assertArrayHasKey('factories', $this->config['dependencies']);

        $factories = $this->config['dependencies']['factories'];
        $this->assertArrayHasKey(DebugBar::class, $factories);
        $this->assertSame(DebugBarFactory::class, $factories[DebugBar::class]);
        $this->assertArrayHasKey(DebugBarExtension::class, $factories);
        $this->assertSame(DebugBarExtensionFactory::class, $factories[DebugBarExtension::class]);
        $this->assertArrayHasKey(DebugBarMiddleware::class, $factories);
        $this->assertSame(DebugBarMiddlewareFactory::class, $factories[DebugBarMiddleware::class]);
    }

    public function testDependenciesHasAliases(): void
    {
        $this->assertArrayHasKey('aliases', $this->config['dependencies']);

        $aliases = $this->config['dependencies']['aliases'];
        $this->assertArrayHasKey(DebugBarInterface::class, $aliases);
        $this->assertSame(DebugBar::class, $aliases[DebugBarInterface::class]);
        $this->assertArrayHasKey(DebugBarMiddlewareInterface::class, $aliases);
        $this->assertSame(DebugBarMiddleware::class, $aliases[DebugBarMiddlewareInterface::class]);
    }
}
