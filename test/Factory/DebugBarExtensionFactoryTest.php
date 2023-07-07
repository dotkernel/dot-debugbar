<?php

declare(strict_types=1);

namespace DotTest\DebugBar\Factory;

use Dot\DebugBar\DebugBarInterface;
use Dot\DebugBar\Extension\DebugBarExtension;
use Dot\DebugBar\Factory\DebugBarExtensionFactory;
use Dot\DebugBar\Factory\DebugBarFactory;
use DotTest\DebugBar\CommonTrait;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DebugBarExtensionFactoryTest extends TestCase
{
    use CommonTrait;

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateExtensionWithoutConfig(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_CONFIG);
        (new DebugBarExtensionFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillNotCreateExtensionWithoutDebugBar(): void
    {
        $container = $this->createMock(ContainerInterface::class);

        $container->method('has')->willReturnMap([
            ['config', true],
            [DebugBarInterface::class, false],
        ]);

        $this->expectExceptionMessage(DebugBarFactory::MESSAGE_MISSING_DEBUG_BAR);
        (new DebugBarExtensionFactory())($container);
    }

    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testWillCreateExtension(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $debugBar  = $this->createMock(DebugBarInterface::class);

        $container->method('has')->willReturnMap([
            ['config', true],
            [DebugBarInterface::class, true],
        ]);

        $container->method('get')->willReturnMap([
            ['config', $this->config],
            [DebugBarInterface::class, $debugBar],
        ]);

        $extension = (new DebugBarExtensionFactory())($container);
        $this->assertInstanceOf(DebugBarExtension::class, $extension);
    }
}
